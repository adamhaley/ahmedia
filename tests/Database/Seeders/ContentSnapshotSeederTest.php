<?php

namespace Tests\Database\Seeders;

use App\Models\CaseStudy;
use App\Models\CaseStudyImage;
use App\Models\Page;
use Database\Seeders\ContentSnapshotSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ContentSnapshotSeeder::class)]
class ContentSnapshotSeederTest extends TestCase
{
    use RefreshDatabase;

    private string $base;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->base = sys_get_temp_dir().'/content-seed-'.uniqid();

        $parent = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => 'about']);
        Page::query()->create(['title' => 'Audit', 'slug' => 'audit', 'template' => 'about', 'parent_id' => $parent->id]);
        Storage::disk('public')->put('case-studies/cover.jpg', 'cover');
        $caseStudy = CaseStudy::factory()->create();
        CaseStudyImage::factory()->create(['case_study_id' => $caseStudy->id, 'image' => 'case-studies/cover.jpg']);

        $this->artisan('content:export', ['--path' => $this->base])->assertSuccessful();

        CaseStudyImage::query()->delete();
        CaseStudy::query()->delete();
        Schema::withoutForeignKeyConstraints(fn () => Page::query()->delete());
        Storage::fake('public');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->base);

        parent::tearDown();
    }

    #[Test]
    public function it_imports_rows_with_their_original_ids_and_foreign_keys(): void
    {
        (new ContentSnapshotSeeder)->run($this->base);

        $audit = Page::query()->where('slug', 'audit')->firstOrFail();
        $this->assertSame('Services', Page::query()->findOrFail($audit->parent_id)->title);
        $this->assertSame(1, CaseStudyImage::query()->where('case_study_id', CaseStudy::query()->value('id'))->count());
    }

    #[Test]
    public function it_copies_the_snapshot_files_to_the_public_disk(): void
    {
        (new ContentSnapshotSeeder)->run($this->base);

        Storage::disk('public')->assertExists('case-studies/cover.jpg');
    }

    #[Test]
    public function it_never_overwrites_a_table_that_already_has_rows(): void
    {
        Page::query()->create(['title' => 'Edited in the CMS', 'slug' => 'edited', 'template' => 'about']);

        (new ContentSnapshotSeeder)->run($this->base);

        $this->assertSame(['Edited in the CMS'], Page::query()->pluck('title')->all());
    }
}
