<?php

namespace Tests\Http\Controllers;

use App\Enums\PageTemplate;
use App\Http\Controllers\PageController;
use App\Models\CaseStudy;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PageController::class)]
class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_published_packages_on_a_service_page(): void
    {
        $page = $this->servicePage();
        Package::factory()->for($page)->create([
            'title' => 'Hacked Site Recovery',
            'price' => 1000,
            'add_ons' => [['title' => 'Clean rebuild on fresh hosting', 'price' => 750, 'days' => 3]],
            'sample_document' => 'packages/recovery-report-sample.pdf',
        ]);

        $response = $this->get($page->url());

        $response->assertOk()
            ->assertSee('Hacked Site Recovery')
            ->assertSee('$1,000')
            ->assertSee('Clean rebuild on fresh hosting')
            ->assertSee('+$750')
            ->assertSee('packages/recovery-report-sample.pdf');
    }

    #[Test]
    public function it_hides_unpublished_packages(): void
    {
        $page = $this->servicePage();
        Package::factory()->for($page)->unpublished()->create(['title' => 'Secret Package']);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('Secret Package');
    }

    #[Test]
    public function it_still_renders_a_bullet_only_service_without_packages(): void
    {
        $page = $this->servicePage(['section_heading' => 'Pricing', 'bullets' => ['$95/hr à la carte']]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('$95/hr à la carte')->assertDontSee('id="packages"', false);
    }

    #[Test]
    public function it_links_case_study_cards_to_their_detail_pages(): void
    {
        $index = Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true]);
        $caseStudy = CaseStudy::factory()->create(['narrative' => 'The full story.']);

        $response = $this->get($index->url());

        $response->assertOk()->assertSee($caseStudy->url())->assertSee('Read the case study');
    }

    #[Test]
    public function it_shows_published_case_studies_on_the_homepage(): void
    {
        $home = $this->homePage();
        Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true, 'hero_heading' => 'Real work, in detail']);
        $caseStudy = CaseStudy::factory()->create(['title' => 'Monk Magazine', 'narrative' => 'The story.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Real work, in detail')->assertSee('Monk Magazine')->assertSee($caseStudy->url());
    }

    #[Test]
    public function it_hides_the_homepage_case_studies_section_when_none_are_published(): void
    {
        $home = $this->homePage();
        Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true]);
        CaseStudy::factory()->create(['is_published' => false]);

        $response = $this->get($home->url());

        $response->assertOk()->assertDontSee('id="case-studies"', false);
    }

    #[Test]
    public function it_takes_the_homepage_contact_copy_from_the_contact_page(): void
    {
        $home = $this->homePage();
        Page::query()->create(['title' => 'Contact', 'slug' => 'contact', 'template' => PageTemplate::Contact, 'is_published' => true, 'hero_heading' => 'Tell us what you need', 'hero_subheading' => 'We reply within a day.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Tell us what you need')->assertSee('We reply within a day.');
    }

    #[Test]
    public function it_takes_the_homepage_hero_from_the_home_page(): void
    {
        $home = $this->homePage(['hero_subheading' => 'Editable from the CMS.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Editable from the CMS.');
    }

    #[Test]
    public function it_shows_active_testimonials_with_their_star_rating_on_the_homepage(): void
    {
        $home = $this->homePage();
        Testimonial::factory()->create(['body' => 'Lightning-fast and skilled.', 'rating' => 4]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Lightning-fast and skilled.')->assertSee('Rated 4 out of 5');
    }

    #[Test]
    public function it_hides_inactive_testimonials(): void
    {
        $home = $this->homePage();
        Testimonial::factory()->inactive()->create(['body' => 'Switched off.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertDontSee('Switched off.')->assertDontSee('id="testimonials"', false);
    }

    #[Test]
    public function it_leaves_out_the_stars_when_a_testimonial_has_no_rating(): void
    {
        $home = $this->homePage();
        Testimonial::factory()->create(['body' => 'An endorsement.', 'rating' => null]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('An endorsement.')->assertDontSee('testimonial-stars', false);
    }

    #[Test]
    public function it_renders_the_doppler_rings_lab_on_the_logo_page(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('data-doppler-lab', false);
    }

    /** @param array<string, mixed> $attributes */
    private function homePage(array $attributes = []): Page
    {
        return Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true, ...$attributes]);
    }

    /** @param array<string, mixed> $attributes */
    private function servicePage(array $attributes = []): Page
    {
        return Page::query()->create([
            'title' => 'Security & Launch',
            'slug' => 'security-and-launch',
            'template' => PageTemplate::Service,
            'is_published' => true,
            ...$attributes,
        ]);
    }
}
