<?php

namespace Henrygodev\LaravelModule\Generators;

class RequestGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $this->command->info("Creating requests {$this->context->name}");

        $this->createFile(
            "{$this->context->modulePath}/Requests/Store{$this->context->name}Request.php",
            $this->context->stubPath("store-request.stub"),
            [
                'namespace' => $this->context->namespace('Requests'),
                'class'     => "Store{$this->context->name}Request"
            ]
        );

        $this->createFile(
            "{$this->context->modulePath}/Requests/Update{$this->context->name}Request.php",
            $this->context->stubPath("update-request.stub"),
            [
                'namespace' => $this->context->namespace('Requests'),
                'class'     => "Update{$this->context->name}Request"
            ]
        );
    }
}