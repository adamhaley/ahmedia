<?php

namespace Tests\Filament\Resources\ContactSubmissions\Pages;

use App\Enums\ContactSubmissionClassification;
use App\Filament\Resources\ContactSubmissions\Pages\ListContactSubmissions;
use App\Models\AuthorizedEmail;
use App\Models\ContactSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(ListContactSubmissions::class)]
class ListContactSubmissionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_hides_spam_by_default(): void
    {
        Queue::fake();
        $user = User::factory()->create();
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);
        $lead = ContactSubmission::factory()->create(['classification' => ContactSubmissionClassification::Valid]);
        $spam = ContactSubmission::factory()->create(['classification' => ContactSubmissionClassification::Spam]);

        Livewire::actingAs($user)
            ->test(ListContactSubmissions::class)
            ->assertCanSeeTableRecords([$lead])
            ->assertCanNotSeeTableRecords([$spam]);
    }
}
