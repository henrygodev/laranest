<?php

namespace Henrygodev\LaravelModule\Commands;

use Exception;
use Henrygodev\LaravelModule\Generators\ControllerGenerator;
use Henrygodev\LaravelModule\Generators\ModelGenerator;
use Henrygodev\LaravelModule\Generators\RequestGenerator;
use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name} {--api : Generate an API controller} {--resource : Generate a resource controller}';

    protected $description = 'Create a new module';

    public function handle(): int
    {
        $context = ModuleContext::from($this->argument('name'));

        if(! $this->isValidName($context->name)){
            $this->error("Invalid module name: \"{$context->name}\". Use PascalCase letters only (e.g ProductCategory).");
            return self::FAILURE;
        }

        $this->createDirectories($context);
        
        $generators = [
            new ModelGenerator($context, $this),
            new ControllerGenerator($context, $this, $this->options()),
            new RequestGenerator($context, $this),
        ];

        try {
            foreach ($generators as $generator) {
                $generator->generate();
            }
        } catch (Exception $e) {
            $this->error("Failed {$e->getMessage()}");
            $this->rollback($generators, $context);
            return self::FAILURE;
        }

        $this->info("Module {$context->moduleName} created successfully");

        return self::SUCCESS;
    }

    private function rollback(array $generators, ModuleContext $context): void
    {
        $this->warn('Rolling back');

        foreach ($generators as $generator) {
            foreach ($generator->getCreatedFiles() as $file) {
                if(file_exists($file)){
                    unlink($file);
                    $this->warn("Removed file: {$file}");
                }
            }
        }

        $directories = [
            "{$context->modulePath}/Models",
            "{$context->modulePath}/Controllers",
            "{$context->modulePath}/Requests",
            $context->modulePath
        ];

        foreach ($directories as $directory) {
            if(is_dir($directory) && $this->isEmptyDirectory($directory)){
                rmdir($directory);
                $this->warn("Removed directory {$directory}");
            }
        }
    }

    private function isEmptyDirectory(string $path): bool
    {
        return count(scandir($path)) === 2;
    }

    private function createDirectories(ModuleContext $context): void
    {
        $directories = [
            $context->modulePath,
            "{$context->modulePath}/Models",
            "{$context->modulePath}/Controllers",
            "{$context->modulePath}/Requests",
        ];
 
        foreach ($directories as $directory) {
            if (! is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
    
    private function isValidName(string $name): bool
    {
        return (bool) preg_match('/^[A-Za-z][A-Za-z0-9]*$/', $name);
    }
}
