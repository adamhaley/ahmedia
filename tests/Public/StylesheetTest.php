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

    #[Test]
    public function it_defines_the_third_accent_colour_in_the_last_root(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        preg_match_all('/:root\s*\{([^}]*)\}/', $css, $matches);

        $this->assertStringContainsString('--color-accent-3: #d9a876;', end($matches[1]));
    }

    #[Test]
    public function it_fills_the_hero_primary_button_with_the_third_accent_on_hover_and_focus(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $bodies = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (in_array('.hero-section .button-primary:hover', $selectors, true) && in_array('.hero-section .button-primary:focus-visible', $selectors, true)) {
                $bodies[] = $body;
            }
        }

        $this->assertNotEmpty($bodies);
        $this->assertStringContainsString('background: var(--color-accent-3)', implode("\n", $bodies));
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
    public function it_draws_the_hero_back_button_in_the_accent_colour(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $bodies = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (in_array('.hero-back', $selectors, true)) {
                $bodies[] = $body;
            }
        }
        $joined = implode("\n", $bodies);

        $this->assertStringContainsString('color: var(--color-accent)', $joined);
        $this->assertStringContainsString('border: 1px solid var(--border-color-strong)', $joined);
    }

    #[Test]
    public function it_highlights_the_hero_back_button_border_on_hover_and_focus(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $bodies = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (in_array('.hero-back:hover', $selectors, true) && in_array('.hero-back:focus-visible', $selectors, true)) {
                $bodies[] = $body;
            }
        }

        $this->assertNotEmpty($bodies);
        $this->assertStringContainsString('border-color: var(--color-accent)', implode("\n", $bodies));
    }

    public static function primaryButtonFinishProvider(): Generator
    {
        yield 'primary button is cyan glass' => ['.button-primary', 'var(--button-glass)'];
        yield 'hero primary button is warm sand' => ['.hero-section .button-primary', 'var(--color-accent-3)'];
        yield 'masthead primary button is pearl' => ['.gradient-masthead .button-primary', 'var(--button-pearl)'];
    }

    #[Test]
    #[DataProvider('primaryButtonFinishProvider')]
    public function it_finishes_the_primary_button_in_glass_or_pearl(string $selector, string $background): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $backgrounds = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (in_array($selector, $selectors, true) && preg_match('/background:\s*([^;]+);/', $body, $m)) {
                $backgrounds[] = trim($m[1]);
            }
        }

        $this->assertSame([$background], $backgrounds);
    }

    #[Test]
    public function it_lights_the_primary_button_border_on_hover_and_focus(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $bodies = [];
        foreach ($this->rules($css) as [$selectors, $body]) {
            if (in_array('.button-primary:hover', $selectors, true) && in_array('.button-primary:focus-visible', $selectors, true)) {
                $bodies[] = $body;
            }
        }

        $this->assertStringContainsString('border-color: var(--color-accent)', implode("\n", $bodies));
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
