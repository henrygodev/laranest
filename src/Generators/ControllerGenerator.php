<?php

namespace Henrygodev\LaravelModule\Generators;

use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Override;

class ControllerGenerator extends BaseGenerator
{
    protected array $options;

    public function __construct(
        ModuleContext $context,
        Command $command,
        array $options = []
    ) {
        parent::__construct($context, $command);
        $this->options = $options;
    }

    public static function fromConfig(ModuleContext $context, Command $command, array $config): static
    {
        $instace = new static($context, $command);
        $instace->config = $config;
        return $instace;
    }

    public function withOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }
    
    public function generate(): void
    {
        $this->command->info("Creating controller {$this->context->name}");

        $stubEntry = $this->resolveStubEntry();
        $className = $this->resolveClassName($stubEntry);
        $filePath = "{$this->context->modulePath}/{$this->config['path']}/{$className}.php";

        $replacements = $this->buildReplacements($className);

        // Resolve
        if(! empty($stubEntry['imports_stub'])){
            $replacements['imports'] = $this->render($this->context->stubPath($stubEntry['imports_stub']), $replacements);
        }

        if(! empty($stubEntry['methods_stub'])){
            $replacements['methods'] = $this->render($this->context->stubPath($stubEntry['methods_stub']), $replacements);
        }

        $this->createFile($filePath, $this->context->stubPath($stubEntry['stub']), $replacements);
    }

    /**
     * Pick the right stub entry based on --api / --resource options.
     * Fall back to the first stub entry (plain controller) if no option is set
     */
    private function resolveStubEntry():array
    {
        $stubs = $this->config['stubs'] ?? [];

        if($this->options['api'] ?? false){
            return collect($stubs)->firstWhere('type', 'api') ?? $stubs[0];
        }
        
        if($this->options['resource'] ?? false){
            return collect($stubs)->firstWhere('type', 'resource') ?? $stubs[0];
        }
        
        return collect($stubs)->firstWhere('type', 'plain') ?? $stubs[0];
    }

    private function buildReplacements(string $className): array
    {
        $ctx = $this->context;
        $ns = $this->config['namespace'] ?? 'Controllers';

        // Resolve requests config entry for namespace
        $requestNs = config('laranest.structure.requests.namespace', 'Requests');

        return [
            'namespace'     => $ctx->namespace($ns),
            'class'         => $className,
            'model'         => $ctx->namespace(config('laranest.structure.models.namespace')) . "\\{$ctx->name}",
            'modelClass'    => $ctx->name,
            'modelVariable' => Str::camel($ctx->name),
            'storeRequest'  => $ctx->namespace($requestNs) . "\\Store{$ctx->name}Request",
            'updateRequest' => $ctx->namespace($requestNs) . "\\Update{$ctx->name}Request",
            'storeRequestClass'  => "Store{$ctx->name}Request",
            'updateRequestClass' => "Update{$ctx->name}Request",
            'imports'       => '',
            'methods'       => '',
        ];

    }
}