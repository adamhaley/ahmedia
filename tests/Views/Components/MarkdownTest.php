<?php

namespace Tests\Views\Components;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class MarkdownTest extends TestCase
{
    #[Test]
    public function it_renders_markdown_as_html(): void
    {
        $view = $this->blade('<x-markdown :text="$text" />', ['text' => "## Why n8n\n\n- **Visual** workflows"]);

        $view->assertSee('<h2>Why n8n</h2>', false)->assertSee('<li><strong>Visual</strong> workflows</li>', false);
    }

    #[Test]
    public function it_escapes_raw_html_in_the_markdown(): void
    {
        $view = $this->blade('<x-markdown :text="$text" />', ['text' => '<script>alert(1)</script>']);

        $view->assertDontSee('<script>', false);
    }
}
