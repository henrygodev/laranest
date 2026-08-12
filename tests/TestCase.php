<?php

namespace Henrygodev\LaravelModule\Tests;

use Henrygodev\LaravelModule\LaravelModuleServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [LaravelModuleServiceProvider::class];
    }

    protected function tearDown(): void
    {
        $this->cleanDirectory(app_path('Modules'));
        $this->cleanDirectory(app_path('Domain'));

        parent::tearDown();
    }

    protected function cleanDirectory(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }
}
