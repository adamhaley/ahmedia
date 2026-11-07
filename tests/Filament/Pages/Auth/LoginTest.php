<?php

namespace Tests\Filament\Pages\Auth;

use App\Filament\Pages\Auth\Login;
use App\Models\AuthorizedEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(Login::class)]
class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_rejects_password_login_outside_local(): void
    {
        $user = $this->authorizedUser();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'break-glass')
            ->call('authenticate')
            ->assertForbidden();

        $this->assertGuest();
    }

    #[Test]
    public function it_allows_password_login_locally(): void
    {
        $this->app['env'] = 'local';
        $user = $this->authorizedUser();

        Livewire::test(Login::class)
            ->set('data.email', $user->email)
            ->set('data.password', 'break-glass')
            ->call('authenticate');

        $this->assertAuthenticatedAs($user);
    }

    private function authorizedUser(): User
    {
        $user = User::factory()->create(['password' => 'break-glass']);
        AuthorizedEmail::query()->create(['email' => $user->email, 'is_active' => true]);

        return $user;
    }
}
