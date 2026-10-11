<?php

namespace Tests\Filament\Resources\Pages\RelationManagers;

use App\Enums\PageTemplate;
use App\Filament\Resources\Pages\Pages\EditPage;
use App\Filament\Resources\Pages\RelationManagers\PackagesRelationManager;
use App\Models\AuthorizedEmail;
use App\Models\Package;
use App\Models\Page;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PackagesRelationManager::class)]
class PackagesRelationManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);
        $this->actingAs($user);
    }

    public static function templateProvider(): Generator
    {
        yield 'service page' => [PageTemplate::Service, true];
        yield 'standard page' => [PageTemplate::Standard, false];
        yield 'contact page' => [PageTemplate::Contact, false];
    }

    #[Test]
    #[DataProvider('templateProvider')]
    public function it_only_shows_packages_on_service_pages(PageTemplate $template, bool $expected): void
    {
        $page = Page::query()->create(['title' => 'Some page', 'slug' => 'some-page', 'template' => $template]);

        $this->assertSame($expected, PackagesRelationManager::canViewForRecord($page, EditPage::class));
    }

    #[Test]
    public function it_lists_a_service_pages_packages(): void
    {
        $page = Page::query()->create(['title' => 'Website Design', 'slug' => 'website-design', 'template' => PageTemplate::Service]);
        $packages = Package::factory()->for($page)->count(2)->create();

        $component = Livewire::test(PackagesRelationManager::class, ['ownerRecord' => $page, 'pageClass' => EditPage::class]);

        $component->assertSuccessful()->assertCanSeeTableRecords($packages);
    }
}
