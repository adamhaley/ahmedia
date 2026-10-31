<?php

namespace Tests\Views\Components;

use App\Enums\PageTemplate;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class HeroTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_draws_doppler_rings_behind_the_homepage_hero(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);

        $response = $this->get('/');

        $response->assertOk()->assertSee('class="doppler-rings"', false);
    }

    #[Test]
    public function it_leaves_rings_off_by_default(): void
    {
        $html = (string) $this->blade('<x-hero title="Services" />');

        $this->assertStringNotContainsString('doppler-rings', $html);
    }
}
