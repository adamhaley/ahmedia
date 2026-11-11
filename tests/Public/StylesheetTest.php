<?php

namespace Tests\Public;

use Generator;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class StylesheetTest extends TestCase
{
    public static function stylesheetProvider(): Generator
    {
        yield 'site stylesheet' => ['styles.css'];
        yield 'doppler rings stylesheet' => ['doppler-rings.css'];
    }

    #[Test]
    #[DataProvider('stylesheetProvider')]
    public function it_has_balanced_braces(string $stylesheet): void
    {
        $css = (string) file_get_contents(public_path($stylesheet));

        $this->assertSame(substr_count($css, '{'), substr_count($css, '}'));
    }

    #[Test]
    public function it_starts_with_the_header_comment(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $this->assertStringStartsWith("/* ahmedia site styles: main stylesheet */\n", $css);
    }

    public static function radiusTokenProvider(): Generator
    {
        yield 'small radius' => ['--radius-sm', '6px'];
        yield 'medium radius' => ['--radius-md', '10px'];
        yield 'large radius' => ['--radius-lg', '16px'];
    }

    #[Test]
    #[DataProvider('radiusTokenProvider')]
    public function it_defines_the_radius_tokens_in_root(string $token, string $value): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        preg_match('/:root\s*\{([^}]*)\}/', $css, $matches);

        $this->assertStringContainsString("{$token}: {$value};", $matches[1] ?? '');
    }

    public static function roundedSelectorProvider(): Generator
    {
        yield 'button' => ['.button'];
        yield 'signup form input' => ['.signup-form input'];
        yield 'contact form input' => ['.contact-form-row input'];
    }

    #[Test]
    #[DataProvider('roundedSelectorProvider')]
    public function it_rounds_controls_with_the_medium_radius(string $selector): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $bodies = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (in_array($selector, $selectors, true)) {
                $bodies[] = $body;
            }
        }
        $joined = implode("\n", $bodies);

        $this->assertTrue(
            str_contains($joined, 'border-radius: var(--radius-md)') && ! str_contains($joined, 'border-radius: 0;'),
        );
    }

    #[Test]
    public function it_keeps_pills_and_circles_off_buttons_inputs_and_cards(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $offenders = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (! preg_match('/border-radius:\s*(999px|50%)/', $body)) {
                continue;
            }
            foreach ($selectors as $selector) {
                if (preg_match('/button|input|textarea|card/', $selector)
                    && ! preg_match('/carousel-indicator|gallery-modal-nav|tag-cloud|icon|avatar|dot|spotlight-media-circle|chat-|codette-carousel-control/', $selector)) {
                    $offenders[] = $selector;
                }
            }
        }

        $this->assertSame([], $offenders);
    }

    /**
     * @return list<array{0: list<string>, 1: string}>
     */
    private function rules(string $css): array
    {
        preg_match_all('/([^{}]+)\{([^{}]*)\}/', $css, $matches, PREG_SET_ORDER);

        return array_map(
            fn (array $m): array => [array_map('trim', explode(',', $m[1])), $m[2]],
            $matches,
        );
    }
}
