<?php

namespace Tests\Views\Components\Layouts;

use App\Enums\PageTemplate;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class SiteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_footer_tagline_from_site_settings(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);
        SiteSetting::current()->update(['footer_tagline' => 'Systems that ship.']);

        $response = $this->get('/');

        $response->assertOk()->assertSee('<p class="footer-copy">Systems that ship.</p>', false);
    }

    #[Test]
    public function it_hides_the_footer_tagline_when_it_is_empty(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);
        SiteSetting::current()->update(['footer_tagline' => null]);

        $response = $this->get('/');

        $response->assertOk()->assertDontSee('footer-copy', false);
    }
}
