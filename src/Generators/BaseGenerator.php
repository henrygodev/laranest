<?php

namespace Henrygodev\LaravelModule\Generators;

use Henrygodev\LaravelModule\Contracts\GeneratorContract;
use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;
use Override;

abstract class BaseGenerator implements GeneratorContract
{
    protected ModuleContext $context;
    protected Command $command;

    /** @var string[] Files created during this generator's run, used for rollback */
    protected array $createdFiles = [];

    /** @var array<string,mixed> Full structure config entry for this generator */
    protected array $config = [];

    public function __construct(ModuleContext $context, Command $command)
    {
        $this->context = $context;
        $this->command = $command;
    }

    /**
     * Return all files created so far by this generator
     *
     * @param array<string,mixed> $config
    */
    public static function fromConfig(ModuleContext $context, Command $command, array $config): static
    {
        $instance           = new static($context, $command);
        $instance->config   = $config;
        return $instance;
    }

    /**
     * Returns all files created so far by this generator.
     *
     * @return string[]
     */
    public function getCreatedFiles(): array
    {
        return $this->createdFiles;
    }

    /**
     * Resolve the class name for a stub entry.
     * Combine prefix + module name + suffix.
     *
     * Example: prefix=Store, name=Product, suffix=Request -> StoreProductRequest
     */
    protected function resolveClassName(array $stub):string
    {
        $prefix = $stub['prefix'] ?? null;
        $suffix = $stub['suffix'] ?? null;

        return "{$prefix}{$this->context->name}{$suffix}";
    }

    /**
     * Render a stub file replacing all placeholders with the given values
    */
    protected function render(string $stub, array $replacements): string
    {
        $content = file_get_contents($stub);

        foreach ($replacements as $key => $value) {
            $content = str_replace(["{{{$key}}}","{{ {$key} }}"], $value, $content);
        }

        return $content;
    }

    /**
     * Write a rendered stub to disk, skipping if the file already exists
    */
    protected function createFile(string $path, string $stub, array $replacements):void
    {
        if(file_exists($path)){
            $this->command->warn("Skipping existing file: {$path}");
            return;
        }

        file_put_contents($path, $this->render($stub, $replacements));
        $this->createdFiles[] = $path;
        $this->command->info("Created: {$path}");

    }

    /**
     * Generate one file per stub entry defined in the config.
     * Resolves class name, path, namespace and stub automatically.
     * Subclasses can override this for special behavior.
     */
    protected function generateFromConfig():void
    {
        $stubs      = $this->config['stubs'] ?? [];
        $path       = $this->config['path'] ?? [];
        $namespace  = $this->config['namespace'] ?? [];

        foreach ($stubs as $stubEntry) {
            $className  = $this->resolveClassName($stubEntry);
            $filePath   = "{$this->context->modulePath}/{$path}/{$className}.php";
            
            $this->createFile($filePath, $this->context->stubPath($stubEntry['stub']), [
                'namespace' => $this->context->namespace($namespace),
                'class'     => $className
            ]);
        }
    }
}
