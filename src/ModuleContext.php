<?php

namespace Henrygodev\LaravelModule;

use Illuminate\Support\Str;

class ModuleContext
{
    public string $name;
    public string $moduleName;
    public string $modulePath;
    public string $basePath;

    public function __construct(
        string $name,
        string $moduleName,
        string $modulePath,
        string $basePath
    )
    {
        $this->name = $name;
        $this->moduleName = $moduleName;
        $this->modulePath = $modulePath;
        $this->basePath = $basePath;
    }
    
    /**
     * Build a ModuleContext from a raw user-provided name
    */
    public static function from(string $input): self
    {
        $name = Str::studly(trim($input));
        $moduleName = Str::pluralStudly($name);

        return new self($name, $moduleName, app_path("Modules/{$moduleName}"), dirname(__DIR__));
    }

    /**
     * Build the fully-qualified namespace for a folder inside the module
    */
    public function namespace(string $folder): string
    {
        return "App\\Modules\\{$this->moduleName}\\{$folder}";
    }
    
    /**
     * Resolve stubs from users or package
    */
    public function stubPath(string $stub): string
    {
        $published = base_path("stubs/laravel-module/{$stub}");

        return file_exists($published)
            ? $published
            : dirname(__DIR__) . "/stubs/{$stub}";
    }
}
