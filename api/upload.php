<?php

declare(strict_types=1);

require __DIR__ . '/_n8n.php';

ahmedia_require_post();

$uploadUrl = ahmedia_env('N8N_UPLOAD_WEBHOOK_URL', AHMEDIA_N8N_UPLOAD_URL);
ahmedia_forward_upload_to_n8n($uploadUrl);
