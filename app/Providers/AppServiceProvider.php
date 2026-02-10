<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Club;   // <-- Importa tus modelos
use App\Models\Event;
use App\Models\Sanction;
use App\Observers\AuditObserver;
use App\Models\Score;


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
        User::observe(AuditObserver::class);
        Club::observe(AuditObserver::class);
        Event::observe(AuditObserver::class);
        Sanction::observe(AuditObserver::class);
        Score::observe(AuditObserver::class);

    }
}
