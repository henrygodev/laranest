<?php

namespace Henrygodev\LaravelModule\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase {

    protected function getPackageProviders($app)
    {
        return parent::getPackageProviders($app);
    }

    protected function tearDown(): void
    {
        $modulePath = app_path('Modules');

        if(is_dir($modulePath)) $this->deleteDirectory($modulePath);
    }

    private function deleteDirectory(string $path): void
    {
        foreach (scandir($path) as $item) {
            if($item === '.' || $item === '..') continue;

            $fullPath = "{$path}/{$item}";

            is_dir($fullPath) ? $this->deleteDirectory($fullPath): unlink($fullPath);
        }

        rmdir($path);
    }
}
