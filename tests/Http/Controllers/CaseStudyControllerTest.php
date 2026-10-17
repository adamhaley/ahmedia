<?php

namespace Tests\Http\Controllers;

use App\Enums\PageTemplate;
use App\Http\Controllers\CaseStudyController;
use App\Models\CaseStudy;
use App\Models\CaseStudyImage;
use App\Models\Package;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(CaseStudyController::class)]
class CaseStudyControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_a_published_case_study_with_its_narrative(): void
    {
        $caseStudy = CaseStudy::factory()->create(['narrative' => "## What we found\n\nA **hidden** backdoor."]);

        $response = $this->get($caseStudy->url());

        $response->assertOk()->assertSee('<h2>What we found</h2>', false)->assertSee('<strong>hidden</strong>', false);
    }

    #[Test]
    public function it_escapes_html_in_the_narrative(): void
    {
        $caseStudy = CaseStudy::factory()->create(['narrative' => '<script>alert(1)</script>']);

        $response = $this->get($caseStudy->url());

        $response->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    #[Test]
    public function it_renders_the_gallery_images(): void
    {
        $caseStudy = CaseStudy::factory()->create();
        CaseStudyImage::factory()->for($caseStudy)->create(['image' => 'case-studies/after.jpg', 'alt_text' => 'The live site after the brand pass']);

        $response = $this->get($caseStudy->url());

        $response->assertOk()->assertSee('case-studies/after.jpg')->assertSee('The live site after the brand pass');
    }

    #[Test]
    public function it_links_to_the_related_package(): void
    {
        $page = Page::query()->create(['title' => 'Website Design', 'slug' => 'website-design', 'template' => PageTemplate::Service]);
        $package = Package::factory()->for($page)->create(['title' => 'Brand Audit', 'slug' => 'brand-audit']);
        $caseStudy = CaseStudy::factory()->create(['package_id' => $package->id]);

        $response = $this->get($caseStudy->url());

        $response->assertOk()->assertSee('Brand Audit')->assertSee('/website-design#brand-audit');
    }

    #[Test]
    public function it_hides_an_unpublished_case_study_from_guests(): void
    {
        $caseStudy = CaseStudy::factory()->create(['is_published' => false]);

        $response = $this->get($caseStudy->url());

        $response->assertNotFound();
    }

    #[Test]
    public function it_shows_an_unpublished_case_study_to_signed_in_users(): void
    {
        $caseStudy = CaseStudy::factory()->create(['is_published' => false, 'title' => 'Draft study']);

        $response = $this->actingAs(User::factory()->create())->get($caseStudy->url());

        $response->assertOk()->assertSee('Draft study');
    }

    #[Test]
    public function it_returns_not_found_for_an_unknown_slug(): void
    {
        $response = $this->get('/case-studies/does-not-exist/');

        $response->assertNotFound();
    }
}
