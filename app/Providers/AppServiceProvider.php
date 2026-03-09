<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('components.sidebar', function ($view) {
            $unreadInboxCount = 0;

            if (auth()->check()) {
                $unreadInboxCount = auth()->user()
                    ->inboxMessages()
                    ->whereNull('read_at')
                    ->count();
            }

            $view->with('unreadInboxCount', $unreadInboxCount);
        });
    }
}
