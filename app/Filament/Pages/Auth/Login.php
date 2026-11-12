<?php

namespace App\Filament\Pages\Auth;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;

/**
 * Google sign-in everywhere. Email and password login is a break-glass for local and for the
 * agent preview site (Google OAuth isn't registered for either), so production accepts Google
 * accounts only.
 */
class Login extends BaseLogin
{
    public function form(Schema $schema): Schema
    {
        return $this->allowsPasswordLogin() ? parent::form($schema) : $schema->components([]);
    }

    public function authenticate(): ?LoginResponse
    {
        abort_unless($this->allowsPasswordLogin(), 403);

        return parent::authenticate();
    }

    /** @return array<Action> */
    protected function getFormActions(): array
    {
        return [
            Action::make('google')
                ->label('Continue with Google')
                ->url(route('auth.google.redirect'))
                ->color('gray'),
            ...($this->allowsPasswordLogin() ? parent::getFormActions() : []),
        ];
    }

    private function allowsPasswordLogin(): bool
    {
        return app()->environment(['local', 'preview']);
    }
}
