<?php

namespace Henrygodev\LaravelModule;

use Henrygodev\LaravelModule\Commands\MakeModuleCommand;
use Illuminate\Support\ServiceProvider;

class LaravelModuleServiceProvider extends ServiceProvider{
    public function boot():void
    {
        if($this->app->runningInConsole()){
            $this->commands([
                MakeModuleCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../stubs' => base_path('stubs/laravel-module'),
            ], 'laravel-module-stubs');

            $this->publishes([
                __DIR__ . '/../config/laranest.php' => config_path('laranest.php'),
            ], 'laravel-module-config');
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laranest.php',
            'laranest'
        );
    }
}