<?php

namespace Tests\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\Pages\ViewContactSubmission;
use App\Models\AuthorizedEmail;
use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ViewContactSubmission::class)]
class ViewContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_marks_a_submission_read_when_opened(): void
    {
        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);
        $submission = ContactSubmission::factory()->create();

        Livewire::actingAs($user)->test(ViewContactSubmission::class, ['record' => $submission->getKey()]);

        $this->assertNotNull($submission->fresh()->read_at);
    }
}
