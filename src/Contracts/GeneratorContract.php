<?php

namespace Henrygodev\LaravelModule\Contracts;

use Henrygodev\LaravelModule\ModuleContext;
use Illuminate\Console\Command;

interface GeneratorContract
{
    public function generate(): void;

    public function getCreatedFiles(): array;

    /**
     * Build a generator instance form a structure config entry
     *
     * @param array<string, mixed> $config Single entry from config('laranest.structure')
     */
    public static function fromConfig(ModuleContext $context, Command $command, array $config): static;
}
