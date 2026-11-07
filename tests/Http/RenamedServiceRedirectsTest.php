<?php

namespace Tests\Http;

use Generator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class RenamedServiceRedirectsTest extends TestCase
{
    public static function renamedServiceProvider(): Generator
    {
        yield 'AI Consulting' => ['/services/ai-consulting', '/services/consultations'];
        yield 'Custom Programming' => ['/services/custom-programming', '/services/automations'];
        yield 'Website Design' => ['/services/website-design', '/services/web-design-and-branding'];
    }

    #[Test]
    #[DataProvider('renamedServiceProvider')]
    public function it_permanently_redirects_a_renamed_service_page(string $oldPath, string $newPath): void
    {
        $response = $this->get($oldPath);

        $response->assertRedirect($newPath)->assertStatus(301);
    }
}
