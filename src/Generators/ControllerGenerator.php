<?php

namespace Henrygodev\LaravelModule\Generators;

use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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

    public function generate(): void
    {
        $this->command->info("Creating controller {$this->context->name}");

        $this->createFile(
            "{$this->context->modulePath}/Controllers/{$this->context->name}Controller.php",
            $this->context->stubPath("controller.stub"),
            $this->buildReplacements()
        );

        file_put_contents(
            storage_path('controller-options.log'),
            json_encode($this->options, JSON_PRETTY_PRINT)
        );
    }

    private function buildReplacements(): array
    {
        // dd($this->options);
        $ctx = $this->context;

        $replacements = [
            'namespace'     => $ctx->namespace('Controllers'),
            'class'         => "{$ctx->name}Controller",
            'model'         => $ctx->namespace('Models') . "\\{$ctx->name}",
            'modelClass'    => $ctx->name,
            'modelVariable' => Str::camel($ctx->name),
            'storeRequest'  => $ctx->namespace('Requests') . "\\Store{$ctx->name}Request",
            'updateRequest' => $ctx->namespace('Requests') . "\\Update{$ctx->name}Request",
            'storeRequestClass'  => "Store{$ctx->name}Request",
            'updateRequestClass' => "Update{$ctx->name}Request",
            'imports'       => '',
            'methods'       => '',
        ];

        if($this->options['api'] ?? false) {
            $replacements['imports'] = $this->render(
                $ctx->stubPath("controller-api-imports.stub"),
                $replacements
            );

            $replacements['methods'] = $this->render(
                $ctx->stubPath("controller-api-methods.stub"),
                $replacements
            );

        }

        if($this->options['resource'] ?? false) {
            $replacements['imports'] = $this->render(
                $ctx->stubPath("controller-api-imports.stub"),
                $replacements
            );

            $replacements['methods'] = $this->render(
                $ctx->stubPath("controller-resource-methods.stub"),
                $replacements
            );
        }
        
        return $replacements;
    }
}