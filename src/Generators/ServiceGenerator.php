<?php

namespace Henrygodev\LaravelModule\Generators;

class ServiceGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $this->command->info("Creating service {$this->context->name}");

        $this->generateFromConfig();
    }

    protected function generateFromConfig(): void
    {
        $stubs      = $this->config['stubs'] ?? [];
        $path       = $this->config['path'] ?? '';
        $namespace  = $this->config['namespace'] ?? '';

        $modelNs = config('laranest.structure.models.namespace', 'Models');

        foreach ($stubs as $stubEntry) {
            $className = $this->resolveClassName($stubEntry);
            $filePath  = "{$this->context->modulePath}/{$path}/{$className}.php";

            $this->createFile($filePath, $this->context->stubPath($stubEntry['stub']), [
                'namespace'       => $this->context->namespace($namespace),
                'class'           => $className,
                'modelNamespace'  => $this->context->namespace($modelNs) . "\\{$this->context->name}",
                'modelClass'      => $this->context->name,
            ]);
        }
    }
}
