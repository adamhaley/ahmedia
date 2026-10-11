<?php

namespace Tests\Http\Controllers;

use App\Enums\PageTemplate;
use App\Http\Controllers\PageController;
use App\Models\CaseStudy;
use App\Models\Package;
use App\Models\Page;
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
