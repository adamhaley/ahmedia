<?php

return [
    // Toggled off for local dev by default; set COMING_SOON_ENABLED=true in
    // production until the real launch, per the 2026-10-01 deploy plan.
    'enabled' => env('COMING_SOON_ENABLED', false),

    // Visiting /preview/{this token} sets a long-lived signed cookie that
    // bypasses the gate for that browser. Laravel's EncryptCookies
    // middleware (on by default) signs/encrypts the cookie value, so a
    // visitor can't forge a valid bypass cookie without the real token.
    'bypass_token' => env('COMING_SOON_BYPASS_TOKEN'),
];
