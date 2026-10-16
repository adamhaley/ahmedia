<?php

namespace App\Services\Chat;

use App\Enums\ChatIntent;

class ChatIntentReply
{
    public function __construct(public readonly ChatIntent $intent, public readonly string $reply) {}
}
