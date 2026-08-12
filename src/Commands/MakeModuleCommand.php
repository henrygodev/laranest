<?php

namespace Henrygodev\LaravelModule\Commands;

use Exception;
use Henrygodev\LaravelModule\Generators\ControllerGenerator;
use Henrygodev\LaravelModule\Generators\MigrationGenerator;
use Henrygodev\LaravelModule\Generators\ModelGenerator;
use Henrygodev\LaravelModule\Generators\RequestGenerator;
use Henrygodev\LaravelModule\Generators\ServiceGenerator;
use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name} {--api : Generate an API controller} {--resource : Generate a resource controller} {--s|service : Generate a service class} {--m|migration : Generate a migration file}';

    protected $description = 'Create a new module';

    public function handle(): int
    {
        $context = ModuleContext::from($this->argument('name'));

        if(! $this->isValidName($context->name)){
            $this->error("Invalid module name: \"{$context->name}\". Use PascalCase letters only (e.g ProductCategory).");
            return self::FAILURE;
        }

        $this->createDirectories($context);
        
        $generators = $this->buildGenerators($context);

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

    private function buildGenerators(ModuleContext $context): array
    {
        $generators = [];

        foreach (config('laranest.structure') as $key => $structureConfig) {
            $generatorClass = $structureConfig['generator'] ?? null;

            if(! $generatorClass || !class_exists($generatorClass)){
                $this->warn("Skipping unknow generator for '{$key}'.");
                continue;
            }

            // Controller
            if(is_a($generatorClass, ControllerGenerator::class, true)){
                $generator = $generatorClass::fromConfig($context, $this, $structureConfig);
                $generators[] = $generator->withOptions($this->options());
                continue;
            }

            $generators[] = $generatorClass::fromConfig($context, $this, $structureConfig);
        }

        if($this->option('service')){
            $serviceConfig = config('laranest.service', [
                'generator' => ServiceGenerator::class,
                'stubs'     => [['stub' => 'service.stub', 'prefix' => null, 'suffix' => 'Service']],
            ]);

            $generatorClass = $serviceConfig['generator'] ?? ServiceGenerator::class;
            $generators[] = $generatorClass::fromConfig($context, $this, $serviceConfig);
        }

        if($this->option('migration')){
            $migrationConfig = config('laranest.migration', [
                'generator' => MigrationGenerator::class,
                'stubs'     => [['stubs' => 'migration.stub']],
            ]);

            $generatorClass = $migrationConfig['generator'] ?? MigrationGenerator::class;
            $generators[] = $generatorClass::fromConfig($context, $this, $migrationConfig);
        }

        return $generators;
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
            "{$context->modulePath}/Services",
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
        $structure = config('laranest.structure', []);

        $directories = [$context->modulePath];
 
        foreach ($structure as $entry) {
            $path = $entry['path'] ?? null;
            if ($path) {
                $directories[] = "{$context->modulePath}/{$path}";
            }
        }

        // Optional generators that create their own subdirectory
        $optionalPaths = [];

        if ($this->option('service')) {
            $optionalPaths[] = config('laranest.service.path', 'Services');
        }

        foreach ($optionalPaths as $path) {
            $directories[] = "{$context->modulePath}/{$path}";
        }

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
