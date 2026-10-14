<?php

namespace Tests\Public;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class StylesheetTest extends TestCase
{
    #[Test]
    public function it_has_balanced_braces(): void
    {
        $css = (string) file_get_contents(public_path('styles.css'));

        $this->assertSame(substr_count($css, '{'), substr_count($css, '}'));
    }
}
