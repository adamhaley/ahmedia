n8n on Digital Ocean:

https://docs.n8n.io/hosting/installation/server-setups/digital-ocean/

Webhook proxy:

- Browser requests go to `/api/chat.php` and `/api/upload.php`
- PHP reads `N8N_WEBHOOK_KEY` from `.env` and forwards requests to n8n
- Optional overrides: `N8N_CHAT_WEBHOOK_URL`, `N8N_UPLOAD_WEBHOOK_URL`
