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
        yield 'hero primary button is pearl' => ['.hero-section .button-primary', 'var(--button-pearl)'];
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

    #[Test]
    public function it_keeps_right_aligned_heroes_right_aligned_on_phones(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $offenders = [];
        foreach ($this->rules($css) as [$selectors, , $media]) {
            if ($media === null || ! str_contains($media, '(max-width: 760px)')) {
                continue;
            }
            foreach (['.hero-align-right .hero-copy', '.hero-align-right .button-row'] as $target) {
                if (in_array($target, $selectors, true)) {
                    $offenders[] = $target;
                }
            }
        }

        $this->assertSame([], $offenders);
    }

    #[Test]
    public function it_hides_back_to_top_with_the_chat_button_on_phones(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $found = false;
        foreach ($this->rules($css) as [$selectors, $body, $media]) {
            if ($media === null || ! str_contains($media, 'max-width: 760px')) {
                continue;
            }
            $hidesChat = array_filter($selectors, fn (string $s): bool => str_contains($s, '.is-scrolling-down') && str_contains($s, '.chat-reopen-button'));
            if ($hidesChat === []) {
                continue;
            }
            $found = true;
            $listsLink = array_filter($selectors, fn (string $s): bool => str_ends_with($s, '.back-to-top-link'));
            $this->assertNotEmpty($listsLink);
            $this->assertDoesNotMatchRegularExpression('/display:\s*none/', $body);
        }

        $this->assertTrue($found);
    }

    #[Test]
    public function it_centres_back_to_top_above_the_chat_button(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $bodies = [];
        foreach ($this->rules($css) as [$selectors, $body, $media]) {
            if ($media === null && in_array('body:has(.chat-widget) .back-to-top-link', $selectors, true)) {
                $bodies[] = $body;
            }
        }
        $joined = implode("\n", $bodies);

        $this->assertNotEmpty($bodies);
        $this->assertMatchesRegularExpression('/right:\s*23px;/', $joined);
        $this->assertMatchesRegularExpression('/bottom:\s*82px;/', $joined);
    }

    /**
     * Walks the CSS character by character, tracking brace depth and the
     * at-rule preludes that enclose each style rule.
     *
     * @return list<array{0: list<string>, 1: string, 2: string|null}> selectors, body, enclosing media query (null at top level)
     */
    private function rules(string $css): array
    {
        $css = (string) preg_replace('~/\*.*?\*/~s', '', $css);
        $rules = [];
        $atRules = [];
        $prelude = '';
        $body = '';
        $inBody = false;

        foreach (str_split($css) as $char) {
            if ($inBody) {
                if ($char === '}') {
                    $media = null;
                    foreach (array_reverse($atRules) as $atRule) {
                        if (str_starts_with($atRule, '@media')) {
                            $media = $atRule;
                            break;
                        }
                    }
                    $rules[] = [array_map('trim', explode(',', $prelude)), $body, $media];
                    $prelude = '';
                    $body = '';
                    $inBody = false;
                } else {
                    $body .= $char;
                }

                continue;
            }

            if ($char === '{') {
                $prelude = trim((string) preg_replace('/\s+/', ' ', $prelude));
                if (str_starts_with($prelude, '@')) {
                    $atRules[] = $prelude;
                    $prelude = '';
                } else {
                    $inBody = true;
                }
            } elseif ($char === '}') {
                array_pop($atRules);
                $prelude = '';
            } else {
                $prelude .= $char;
            }
        }

        return $rules;
    }
}
