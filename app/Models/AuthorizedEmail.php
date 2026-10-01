<?php

namespace App\Models;

use Database\Factories\AuthorizedEmailFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['email', 'is_active'])]
class AuthorizedEmail extends Model
{
    /** @use HasFactory<AuthorizedEmailFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (AuthorizedEmail $authorizedEmail): void {
            $authorizedEmail->email = Str::lower(trim($authorizedEmail->email));
        });
    }
}
