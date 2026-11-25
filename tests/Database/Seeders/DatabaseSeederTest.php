<?php

namespace Tests\Database\Seeders;

use App\Models\FaqItem;
use App\Models\Page;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(DatabaseSeeder::class)]
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    #[Test]
    public function it_seeds_the_committed_snapshot_into_an_empty_database(): void
    {
        $snapshot = json_decode(File::get(database_path('seeders/content/snapshot.json')), true, flags: JSON_THROW_ON_ERROR);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame(count($snapshot['pages']), Page::query()->count());
        $this->assertSame(count($snapshot['faq_items']), FaqItem::query()->count());
    }

    #[Test]
    public function it_never_overwrites_edited_content_on_a_second_run(): void
    {
        $this->seed(DatabaseSeeder::class);
        FaqItem::query()->orderBy('id')->first()->update(['answer' => '<p>Edited in the CMS</p>']);

        $this->seed(DatabaseSeeder::class);

        $this->assertSame('<p>Edited in the CMS</p>', FaqItem::query()->orderBy('id')->value('answer'));
    }

    #[Test]
    public function it_publishes_every_snapshot_image_to_the_public_disk(): void
    {
        $this->seed(DatabaseSeeder::class);

        Storage::disk('public')->assertExists('projects/ai-assistant-core.webp');
    }
}
