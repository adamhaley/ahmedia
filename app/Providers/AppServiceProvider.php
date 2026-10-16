<?php

namespace App\Providers;

use App\Models\FaqItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewInstance;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('components.chat-widget', function (ViewInstance $view): void {
            $view->with('chatFaqs', FaqItem::query()
                ->orderBy('sort_order')->orderBy('id')
                ->get(['id', 'question', 'answer']));
        });
    }
}
