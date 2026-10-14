<?php

namespace Tests\Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Package;
use App\Models\Page;
use Database\Seeders\AhmediaContentSeeder;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\PackageAndCaseStudyContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DatabaseSeeder::class)]
#[CoversClass(AhmediaContentSeeder::class)]
#[CoversClass(PackageAndCaseStudyContentSeeder::class)]
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
        $this->seed(DatabaseSeeder::class);

        $content = Page::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .Package::query()->get()->toJson(JSON_UNESCAPED_UNICODE)
            .CaseStudy::query()->get()->toJson(JSON_UNESCAPED_UNICODE);

        $this->assertStringNotContainsString('—', $content);
    }
}
