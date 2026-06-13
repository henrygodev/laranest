<?php

namespace Henrygodev\LaravelModule\Generators;

use Illuminate\Support\Str;

class MigrationGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $table      = Str::snake($this->context->moduleName);
        $fileName   = date('Y_m_d_His')."_create_{$table}_table.php";
        $path       = database_path("migrations/{$fileName}");
        $className = 'Create' . $this->context->moduleName . 'Table';

        $this->command->info("Creating migration {$fileName}");

        $this->createFile($path, $this->context->stubPath('migration.stub'),['table'=> $table, 'class' => $className]);
    }
}
