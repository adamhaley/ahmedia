<?php

declare(strict_types=1);

require __DIR__ . '/_n8n.php';

ahmedia_require_post();

$payload = file_get_contents('php://input');

if ($payload === false || $payload === '') {
    ahmedia_json_error('Missing request body.', 400);
}

$decoded = json_decode($payload, true);
if (!is_array($decoded)) {
    ahmedia_json_error('Invalid JSON body.', 400);
}

$chatUrl = ahmedia_env('N8N_CHAT_WEBHOOK_URL', AHMEDIA_N8N_CHAT_URL);
ahmedia_stream_json_to_n8n($chatUrl, $payload);
