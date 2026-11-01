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
}
