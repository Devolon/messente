<?php

namespace Devolon\Messente;

use Devolon\Messente\Services\MessenteSMSSenderService;
use Devolon\Sms\Services\Contracts\SMSSenderServiceInterface;
use Illuminate\Support\ServiceProvider;

class MessenteServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('messente.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'messente');

        $this->app->singleton(MessenteSMSSenderService::class);
        $this->app->tag(MessenteSMSSenderService::class, SMSSenderServiceInterface::class);
    }
}
