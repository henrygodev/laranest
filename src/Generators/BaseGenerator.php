<?php

namespace Henrygodev\LaravelModule\Generators;

use Henrygodev\LaravelModule\Contracts\GeneratorContract;
use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;

abstract class BaseGenerator implements GeneratorContract
{
    protected ModuleContext $context;
    protected Command $command;

    protected array $createdFiles = [];

    public function __construct(ModuleContext $context, Command $command)
    {
        $this->context = $context;
        $this->command = $command;
    }

    /**
     * Return all files created so far by this generator
    */
    public function getCreatedFiles(): array
    {
        return $this->createdFiles;
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
}