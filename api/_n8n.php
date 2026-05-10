<?php

declare(strict_types=1);

const AHMEDIA_ENV_CACHE = '__ahmedia_env_cache';
const AHMEDIA_N8N_CHAT_URL = 'https://n8n.ahmedia.ai/webhook/rag_chat';
const AHMEDIA_N8N_UPLOAD_URL = 'https://n8n.ahmedia.ai/webhook/ingest_file';

function ahmedia_load_env(string $envPath): array
{
    static $cache = null;

    if ($cache !== null) {
        return $cache;
    }

    $values = [];

    if (!is_file($envPath) || !is_readable($envPath)) {
        $cache = $values;
        return $cache;
    }

    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $trimmed = trim($line);

        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        $parts = explode('=', $trimmed, 2);

        if (count($parts) !== 2) {
            continue;
        }

        $key = trim($parts[0]);
        $value = trim($parts[1]);

        if ($key === '') {
            continue;
        }

        $length = strlen($value);
        if ($length >= 2) {
            $first = $value[0];
            $last = $value[$length - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        $values[$key] = $value;
    }

    $cache = $values;

    return $cache;
}

function ahmedia_env(string $key, ?string $default = null): ?string
{
    $envPath = dirname(__DIR__) . '/.env';
    $values = ahmedia_load_env($envPath);

    if (array_key_exists($key, $values)) {
        return $values[$key];
    }

    $serverValue = $_SERVER[$key] ?? getenv($key);
    if ($serverValue !== false && $serverValue !== null && $serverValue !== '') {
        return (string) $serverValue;
    }

    return $default;
}

function ahmedia_require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        ahmedia_json_error('Method not allowed.', 405);
    }
}

function ahmedia_json_error(string $message, int $status = 500): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => $message], JSON_UNESCAPED_SLASHES);
    exit;
}

function ahmedia_get_n8n_headers(): array
{
    $webhookKey = ahmedia_env('N8N_WEBHOOK_KEY');

    if ($webhookKey === null || $webhookKey === '') {
        ahmedia_json_error('Missing N8N webhook configuration.', 500);
    }

    return [
        'X-Webhook-Key: ' . $webhookKey,
    ];
}

function ahmedia_configure_curl(string $url, array $headers): CurlHandle
{
    if (!function_exists('curl_init')) {
        ahmedia_json_error('cURL is not available on this server.', 500);
    }

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    return $ch;
}

function ahmedia_stream_json_to_n8n(string $url, string $payload): void
{
    $headers = array_merge(
        ahmedia_get_n8n_headers(),
        ['Content-Type: application/json']
    );

    $contentType = 'application/json; charset=utf-8';
    $statusCode = 200;
    $ch = ahmedia_configure_curl($url, $headers);

    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, static function ($ch, string $header) use (&$contentType, &$statusCode): int {
        $length = strlen($header);

        if (preg_match('/^HTTP\/\d+(?:\.\d+)?\s+(\d{3})\b/i', trim($header), $matches) === 1) {
            $statusCode = (int) $matches[1];
            http_response_code($statusCode);
        }

        $parts = explode(':', $header, 2);

        if (count($parts) === 2 && strtolower(trim($parts[0])) === 'content-type') {
            $contentType = trim($parts[1]);
        }

        return $length;
    });

    $headersSent = false;
    curl_setopt($ch, CURLOPT_WRITEFUNCTION, static function ($ch, string $chunk) use (&$headersSent, &$contentType): int {
        if (!$headersSent) {
            header('Content-Type: ' . $contentType);
            header('Cache-Control: no-store');
            $headersSent = true;
        }

        echo $chunk;
        flush();

        return strlen($chunk);
    });

    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    ob_implicit_flush(true);

    $success = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if ($success === false) {
        $error = curl_error($ch);
        curl_close($ch);
        ahmedia_json_error('Failed to contact n8n: ' . $error, 502);
    }

    curl_close($ch);
    http_response_code($status > 0 ? $status : $statusCode);
}

function ahmedia_forward_upload_to_n8n(string $url): void
{
    if (!isset($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        ahmedia_json_error('Missing uploaded file.', 400);
    }

    $tmpPath = $_FILES['file']['tmp_name'];
    $fileName = $_FILES['file']['name'] ?? 'upload.bin';
    $mimeType = $_FILES['file']['type'] ?? 'application/octet-stream';

    $postFields = [
        'file' => new CURLFile($tmpPath, $mimeType, $fileName),
    ];

    if (isset($_POST['namespace']) && $_POST['namespace'] !== '') {
        $postFields['namespace'] = (string) $_POST['namespace'];
    }

    $ch = ahmedia_configure_curl($url, ahmedia_get_n8n_headers());
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

    $response = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        ahmedia_json_error('Failed to contact n8n: ' . $error, 502);
    }

    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'application/json; charset=utf-8';
    curl_close($ch);

    http_response_code($status > 0 ? $status : 200);
    header('Content-Type: ' . $contentType);
    header('Cache-Control: no-store');
    echo $response;
}
