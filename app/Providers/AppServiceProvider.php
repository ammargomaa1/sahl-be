<?php

namespace App\Providers;

use App\Cart\Cart;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->singleton(Cart::class, function ($app) {
            if ($app->auth->user()) {
                $app->auth->user()->load([
                                'cart.stock'
                            ]);
            }

            return new Cart($app->auth->user());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (User $user, string $token) {
            return env('APP_URL') . '/reset-password?token='.$token . '&email=' . $user->email;
        });
    }
}
