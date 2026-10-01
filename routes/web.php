<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', PageController::class)->name('page.home');
Route::fallback(PageController::class)->name('page.show');
