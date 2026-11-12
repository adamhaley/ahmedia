<?php

namespace Database\Seeders;

use App\Models\AuthorizedEmail;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * The agent preview site's admin login: one user, authorized for the panel, with the password
 * the preview build generates. The preview build runs this after loading content; it does
 * nothing outside APP_ENV=preview.
 */
class PreviewSeeder extends Seeder
{
    public const EMAIL = 'preview-admin@ahmedia.ai';

    public function run(): void
    {
        $password = config('app.preview_admin_password');

        if (! app()->environment('preview') || ! is_string($password) || $password === '') {
            return;
        }

        User::query()->updateOrCreate(
            ['email' => self::EMAIL],
            ['name' => 'Preview admin', 'password' => $password, 'email_verified_at' => now()],
        );
        AuthorizedEmail::query()->updateOrCreate(['email' => self::EMAIL], ['is_active' => true]);
    }
}
