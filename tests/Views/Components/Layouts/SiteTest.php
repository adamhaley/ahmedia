<?php

namespace Tests\Views\Components\Layouts;

use App\Enums\PageTemplate;
use App\Models\Page;
use App\Models\SiteSetting;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function footerLinkProvider(): Generator
    {
        yield 'facebook' => ['facebook_url', 'https://facebook.com/ahmedia', '<a href="https://facebook.com/ahmedia" aria-label="Facebook">'];
        yield 'instagram' => ['instagram_url', 'https://instagram.com/ahmedia.ai', '<a href="https://instagram.com/ahmedia.ai" aria-label="Instagram">'];
        yield 'linkedin' => ['linkedin_url', 'https://linkedin.com/company/ahmedia', '<a href="https://linkedin.com/company/ahmedia" aria-label="LinkedIn">'];
        yield 'email' => ['contact_email', 'adam@ahmedia.ai', '<a href="mailto:adam@ahmedia.ai" aria-label="Email adam@ahmedia.ai"'];
    }

    #[Test]
    #[DataProvider('footerLinkProvider')]
    public function it_links_each_filled_site_setting_in_the_footer(string $field, string $value, string $expectedLink): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);
        SiteSetting::current()->update([$field => $value]);

        $response = $this->get('/');

        $response->assertOk()->assertSee($expectedLink, false);
    }

    #[Test]
    public function it_shows_no_footer_icons_when_the_links_are_empty(): void
    {
        Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true]);
        SiteSetting::current()->update(['contact_email' => null, 'facebook_url' => null, 'instagram_url' => null, 'linkedin_url' => null]);

        $response = $this->get('/');

        $response->assertOk()->assertDontSee('<svg viewBox="0 0 512 512"', false)->assertDontSee('aria-label="Instagram"', false);
    }
}
