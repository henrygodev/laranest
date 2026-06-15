<?php

namespace Henrygodev\LaravelModule\Generators;

class RequestGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $this->command->info("Creating requests {$this->context->name}");

        $this->generateFromConfig();
    }
}