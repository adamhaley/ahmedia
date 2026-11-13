<?php

namespace Tests\Database\Seeders;

use App\Enums\PageTemplate;
use App\Models\CaseStudy;
use App\Models\FaqItem;
use App\Models\Package;
use App\Models\Page;
use App\Models\Project;
use App\Models\Testimonial;
use Database\Seeders\AhmediaContentSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\FaqContentSeeder;
use Database\Seeders\PackageAndCaseStudyContentSeeder;
use Database\Seeders\ProjectContentSeeder;
use Database\Seeders\TestimonialContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DatabaseSeeder::class)]
#[CoversClass(AhmediaContentSeeder::class)]
#[CoversClass(PackageAndCaseStudyContentSeeder::class)]
#[CoversClass(FaqContentSeeder::class)]
#[CoversClass(TestimonialContentSeeder::class)]
#[CoversClass(ProjectContentSeeder::class)]
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    #[Test]
    public function it_seeds_an_empty_database(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Page::query()->where('path', 'services/security-and-launch')->exists());
        $this->assertSame(3, Package::query()->count());
        $this->assertSame(2, CaseStudy::query()->count());
    }

    #[Test]
    public function it_gives_the_seeded_homepage_the_homepage_rings_style(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame('Homepage', Page::query()->where('path', '')->first()->dopplerRingStyle?->name);
    }

    #[Test]
    public function it_seeds_the_faqs_and_the_faq_page(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(18, FaqItem::query()->count());
        $this->assertSame(PageTemplate::Faq, Page::query()->where('path', 'faq')->value('template'));
    }

    #[Test]
    public function it_adds_faqs_to_a_site_that_already_has_pages(): void
    {
        $this->seed(DatabaseSeeder::class);
        FaqItem::query()->delete();
        Page::query()->where('path', 'faq')->delete();

        $this->seed(DatabaseSeeder::class);

        $this->assertTrue(Page::query()->where('path', 'faq')->exists());
    }

    #[Test]
    public function it_never_overwrites_edited_faqs(): void
    {
        $this->seed(DatabaseSeeder::class);
        FaqItem::query()->orderBy('id')->first()->update(['answer' => '<p>Edited in the CMS</p>']);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame('<p>Edited in the CMS</p>', FaqItem::query()->orderBy('id')->value('answer'));
    }

    #[Test]
    public function it_seeds_the_testimonials(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(7, Testimonial::query()->count());
    }

    #[Test]
    public function it_seeds_the_workflow_projects_on_the_automations_page(): void
    {
        $automations = $this->seedWithAutomationsPage();

        $this->assertSame(7, $automations->projects()->published()->count());
    }

    #[Test]
    public function it_publishes_the_project_images_to_the_public_disk(): void
    {
        $this->seedWithAutomationsPage();

        Storage::disk('public')->assertExists(Project::query()->pluck('cover_image')->all());
    }

    #[Test]
    public function it_never_overwrites_edited_projects(): void
    {
        $this->seedWithAutomationsPage();
        Project::query()->where('slug', 'ai-assistant-core')->update(['title' => 'Edited in the CMS']);

        $this->seed(ProjectContentSeeder::class);

        $this->assertSame('Edited in the CMS', Project::query()->where('slug', 'ai-assistant-core')->value('title'));
    }

    #[Test]
    public function it_never_overwrites_edited_pages(): void
    {
        $this->seed(DatabaseSeeder::class);
        Page::query()->where('path', 'about')->update(['hero_heading' => 'Edited in the CMS']);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame('Edited in the CMS', Page::query()->where('path', 'about')->value('hero_heading'));
    }

    #[Test]
    public function it_never_overwrites_edited_packages(): void
    {
        $this->seed(DatabaseSeeder::class);
        Package::query()->where('slug', 'brand-audit')->update(['price' => 650]);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(650, Package::query()->where('slug', 'brand-audit')->value('price'));
    }

    #[Test]
    public function it_does_not_duplicate_content_when_run_twice(): void
    {
        $this->seed(DatabaseSeeder::class);
        $pages = Page::query()->count();

        $this->seed(DatabaseSeeder::class);

        $this->assertSame($pages, Page::query()->count());
    }

    #[Test]
    public function it_contains_no_em_dashes(): void
    {
        $this->seedWithAutomationsPage();

        $content = Page::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .Package::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .CaseStudy::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .FaqItem::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .Testimonial::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .Project::query()->get()->toJson(JSON_UNESCAPED_UNICODE);

        $this->assertStringNotContainsString('—', $content);
    }

    /**
     * The page seeder predates the Automations page, which a later migration adds,
     * so tests create it before seeding the projects that hang off it.
     */
    private function seedWithAutomationsPage(): Page
    {
        $this->seed(DatabaseSeeder::class);
        $automations = Page::query()->create([
            'parent_id' => Page::query()->where('path', 'services')->value('id'),
            'slug' => 'automations',
            'title' => 'Automations',
            'template' => PageTemplate::Service,
        ]);
        $this->seed(ProjectContentSeeder::class);

        return $automations;
    }
}
