<?php

namespace Tests\Console\Commands;

use App\Console\Commands\ExportContent;
use App\Models\CaseStudy;
use App\Models\CaseStudyImage;
use App\Models\Page;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ExportContent::class)]
class ExportContentTest extends TestCase
{
    use RefreshDatabase;

    private string $base;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        $this->base = sys_get_temp_dir().'/content-export-'.uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->base);

        parent::tearDown();
    }

    /**
     * @return array<string, mixed>
     */
    private function export(): array
    {
        $this->artisan('content:export', ['--path' => $this->base])->assertSuccessful();

        return json_decode(File::get("{$this->base}/snapshot.json"), true, flags: JSON_THROW_ON_ERROR);
    }

    #[Test]
    public function it_writes_content_tables_ordered_by_id_with_raw_json_columns(): void
    {
        Page::query()->create(['title' => 'Home', 'template' => 'home', 'hero_points' => ['One', 'Two']]);
        Page::query()->create(['title' => 'About', 'slug' => 'about', 'template' => 'about']);

        $snapshot = $this->export();

        $this->assertSame(['Home', 'About'], array_column($snapshot['pages'], 'title'));
        $this->assertSame(['One', 'Two'], json_decode($snapshot['pages'][0]['hero_points'], true));
        $this->assertIsString($snapshot['pages'][0]['hero_points']);
        $this->assertArrayHasKey('exported_at', $snapshot);
    }

    #[Test]
    public function it_exports_only_the_content_tables(): void
    {
        User::factory()->create();

        $snapshot = $this->export();

        $this->assertSame([...ExportContent::Tables, 'exported_at'], array_keys($snapshot));
    }

    #[Test]
    public function it_exports_only_published_posts(): void
    {
        Post::factory()->create(['title' => 'Live']);
        Post::factory()->draft()->create();
        Post::factory()->scheduled()->create();

        $this->assertSame(['Live'], array_column($this->export()['posts'], 'title'));
    }

    #[Test]
    public function it_copies_the_files_the_rows_reference(): void
    {
        Storage::disk('public')->put('case-studies/cover.jpg', 'cover');
        $caseStudy = CaseStudy::factory()->create();
        CaseStudyImage::factory()->create(['case_study_id' => $caseStudy->id, 'image' => 'case-studies/cover.jpg']);

        $this->export();

        $this->assertSame('cover', File::get("{$this->base}/case-studies/cover.jpg"));
    }

    #[Test]
    public function it_removes_files_that_are_no_longer_referenced(): void
    {
        File::ensureDirectoryExists("{$this->base}/case-studies");
        File::put("{$this->base}/case-studies/stale.jpg", 'stale');

        $this->export();

        $this->assertFileDoesNotExist("{$this->base}/case-studies/stale.jpg");
    }
}
