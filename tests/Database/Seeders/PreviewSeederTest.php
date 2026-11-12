<?php

namespace Tests\Database\Seeders;

use App\Models\AuthorizedEmail;
use App\Models\User;
use Database\Seeders\PreviewSeeder;
use Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PreviewSeeder::class)]
class PreviewSeederTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_authorized_admin_on_the_preview_site(): void
    {
        $this->app['env'] = 'preview';
        config(['app.preview_admin_password' => 'preview-secret']);

        (new PreviewSeeder)->run();

        $user = User::query()->where('email', PreviewSeeder::EMAIL)->sole();
        $this->assertTrue(Hash::check('preview-secret', $user->password));
        $this->assertTrue(AuthorizedEmail::query()->where('email', PreviewSeeder::EMAIL)->where('is_active', true)->exists());
    }

    public static function skippedProvider(): Generator
    {
        yield 'production, with a password' => ['production', 'preview-secret'];
        yield 'local, with a password' => ['local', 'preview-secret'];
        yield 'preview, without a password' => ['preview', null];
        yield 'preview, with an empty password' => ['preview', ''];
    }

    #[Test]
    #[DataProvider('skippedProvider')]
    public function it_creates_nobody_outside_a_configured_preview(string $env, ?string $password): void
    {
        $this->app['env'] = $env;
        config(['app.preview_admin_password' => $password]);

        (new PreviewSeeder)->run();

        $this->assertDatabaseMissing('users', ['email' => PreviewSeeder::EMAIL]);
    }
}
