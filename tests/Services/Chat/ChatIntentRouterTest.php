<?php

namespace Tests\Services\Chat;

use App\Services\Chat\ChatIntentRouter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ChatIntentRouterTest extends TestCase
{
    #[Test]
    #[DataProvider('phrases')]
    public function it_only_routes_complete_small_talk_phrases(string $message, ?string $expected): void
    {
        $reply = (new ChatIntentRouter)->route($message);

        $this->assertSame($expected, $reply?->intent->value);
    }

    public static function phrases(): array
    {
        return [
            'greeting' => ['hello', 'greeting'],
            'case and punctuation' => ['  HI THERE!!!  ', 'greeting'],
            'whitespace' => ["Good\n morning!", 'greeting'],
            'gratitude' => ['Thank you very much.', 'farewell'],
            'farewell' => ['Have a nice day!', 'farewell'],
            'goodbye' => ['bye', 'farewell'],
            'greeting with question' => ['Hi, how much does a show cost?', null],
            'thanks with question' => ['Thanks, do you travel?', null],
            'prefix collision' => ['high ceilings', null],
            'unrelated' => ['I want a cookie', null],
            'emoji' => ['🎪', null],
            'empty' => ['', null],
        ];
    }
}
