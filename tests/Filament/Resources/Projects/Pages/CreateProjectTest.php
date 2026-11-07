<?php

namespace Tests\Filament\Resources\Projects\Pages;

use App\Enums\PageTemplate;
use App\Enums\ProjectType;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Models\AuthorizedEmail;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(CreateProject::class)]
class CreateProjectTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);
        $this->actingAs($user);
    }

    #[Test]
    public function it_creates_a_project_on_a_service_page_with_its_links(): void
    {
        $page = Page::query()->create(['title' => 'Automations', 'slug' => 'automations', 'template' => PageTemplate::Service]);

        Livewire::test(CreateProject::class)
            ->fillForm([
                'title' => 'Lead Intake Workflow',
                'slug' => 'lead-intake-workflow',
                'type' => ProjectType::Workflow->value,
                'page_id' => $page->id,
                'links' => [['label' => 'Buy on Gumroad', 'url' => 'https://gumroad.com/l/lead-intake']],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertEquals(
            [['label' => 'Buy on Gumroad', 'url' => 'https://gumroad.com/l/lead-intake']],
            $page->projects()->sole()->links,
        );
    }
}
