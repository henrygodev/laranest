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
        $packageConfig = require __DIR__ . '/../config/laranest.php';

        $this->app->booting(function () use ($packageConfig) {
            $userConfig = $this->app['config']->get('laranest', []);
            $merged     = array_replace_recursive($packageConfig, $userConfig);

            $this->app['config']->set('laranest', $merged);
        });
    }
}