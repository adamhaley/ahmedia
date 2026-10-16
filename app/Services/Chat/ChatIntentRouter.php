<?php

namespace App\Services\Chat;

use App\Enums\ChatIntent;

class ChatIntentRouter
{
    public function route(string $message): ?ChatIntentReply
    {
        $normalized = preg_replace('/\s+/u', ' ', mb_strtolower(trim($message)));
        $normalized = preg_replace('/[\s\p{P}]+$/u', '', $normalized);

        return match ($normalized) {
            'hi', 'hello', 'hey', 'hi there', 'hello there', 'hey there',
            'good morning', 'good afternoon', 'good evening' => new ChatIntentReply(
                ChatIntent::Greeting,
                'Hi! I can help with questions about our services, pricing, packages, and how projects work. What would you like to know?',
            ),
            'thanks', 'thank you', 'thanks a lot', 'thank you very much',
            'thanks so much', 'thank you so much', 'bye', 'goodbye', 'bye bye',
            'see you', 'take care', 'have a nice day', 'have a great day', 'thanks bye' => new ChatIntentReply(
                ChatIntent::Farewell,
                "Thanks for stopping by! If another question comes up, I'm here to help.",
            ),
            default => null,
        };
    }
}
