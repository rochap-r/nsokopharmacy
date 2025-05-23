<?php

namespace App\Providers;

use App\Http\Middleware\TenantMiddleware;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TenantServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('tenant', function () {
            return null;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Enregistrer le middleware
        $this->app->bind('tenant.middleware', TenantMiddleware::class);

        // Définir un helper pour accéder au tenant courant
        $this->app->bind('current_tenant', function (Application $app) {
            return $app->make('tenant');
        });

        // Ajouter le middleware à la première position du groupe web
        $router = $this->app['router'];
        $router->aliasMiddleware('tenant', TenantMiddleware::class);
    }
}
