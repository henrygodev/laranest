<?php

namespace Henrygodev\LaravelModule\Generators;

class ModelGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $this->command->info("Creating model {$this->context->name}");

        $this->createFile(
            "{$this->context->modulePath}/Models/{$this->context->name}.php",
            $this->context->stubPath('model.stub'),
            [
                'namespace' => $this->context->namespace("Models"),
                'class'     => $this->context->name,
            ]
            );
    }
}