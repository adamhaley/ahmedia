<?php

namespace Tests\Filament\Pages\Auth;

use App\Filament\Pages\Auth\Login;
use App\Models\AuthorizedEmail;
use App\Models\User;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function breakGlassEnvironmentProvider(): Generator
    {
        yield 'local' => ['local'];
        yield 'agent preview site' => ['preview'];
    }

    #[Test]
    #[DataProvider('breakGlassEnvironmentProvider')]
    public function it_allows_password_login_in_break_glass_environments(string $env): void
    {
        $this->app['env'] = $env;
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
