<?php

namespace Henrygodev\LaravelModule;

use Illuminate\Support\Str;

class ModuleContext
{
    public string $name;
    public string $moduleName;
    public string $modulePath;
    public string $basePath;
    public string $baseNamespace;

    public function __construct(
        string $name,
        string $moduleName,
        string $modulePath,
        string $basePath,
        string $baseNamespace
    )
    {
        $this->name = $name;
        $this->moduleName = $moduleName;
        $this->modulePath = $modulePath;
        $this->basePath = $basePath;
        $this->baseNamespace = $baseNamespace;
    }
    
    /**
     * Build a ModuleContext from a raw user-provided name
    */
    public static function from(string $input): self
    {
        $name = Str::studly(trim($input));
        $moduleName = Str::pluralStudly($name);

        $modulePath =config('laranest.modules_path', 'Modules');
        $moduleNamespace =config('laranest.modules_namespace', 'App\\Modules');

        return new self(
            $name, $moduleName, app_path("{$modulePath}/{$moduleName}"), dirname(__DIR__), "{$moduleNamespace}\\{$moduleName}"
        );

    }

    /**
     * Build the fully-qualified namespace for a folder inside the module
    */
    public function namespace(string $folder): string
    {
        $folder = str_replace('/', '\\', $folder);
        return "{$this->baseNamespace}\\{$folder}";
    }
    
    /**
     * Resolve stubs from users or package
    */
    public function stubPath(string $stub): string
    {
        $published = base_path("stubs/laravel-module/{$stub}");

        return file_exists($published)
            ? $published
            : "{$this->basePath}/stubs/{$stub}";
    }
}
