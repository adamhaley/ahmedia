<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('auth.google.callback');

// Lets Adam browse the real site while the coming-soon gate is on for
// everyone else -- 404s on a wrong/missing token rather than 403, so it
// doesn't even hint the route exists. See config/coming-soon.php.
Route::get('/preview/{token}', function (string $token) {
    abort_unless(
        config('coming-soon.bypass_token') && hash_equals((string) config('coming-soon.bypass_token'), $token),
        404
    );

    return redirect('/')->withCookie(cookie('coming_soon_bypass', 'granted', 60 * 24 * 365));
})->name('coming-soon.preview');

Route::get('/', PageController::class)->name('page.home');
Route::fallback(PageController::class)->name('page.show');
