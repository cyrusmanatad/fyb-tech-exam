<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use App\Listeners\LogUserLogin;
use App\Models\Product;
use App\Models\User;
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
         // Seeded role name is "Super Admin". Gate::before does not run for Spatie route middleware.
         Gate::before(function ($user, $ability) {
             if ($user->hasRole(User::ROLE_SUPER_ADMIN)) {
                 return true;
             }
         });

         Product::observe(ProductVariantObserver::class);
         Event::listen(Login::class, LogUserLogin::class);
    }
}
