<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\LogUserLogin;
use App\Models\Product;
use App\Observers\ProductVariantObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
         // Implicitly grant "super-admin" role all permission checks using can()
         Gate::before(function ($user, $ability) {
             if ($user->hasRole('super-admin')) {
                 return true;
             }
         });

         Product::observe(ProductVariantObserver::class);
         Event::listen(Login::class, LogUserLogin::class);
    }
}
