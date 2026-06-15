<?php

namespace Henrygodev\LaravelModule\Generators;

class ModelGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $this->command->info("Creating model {$this->context->name}");

        $this->generateFromConfig();
    }
}