<?php

namespace Henrygodev\LaravelModule\Generators;

use Illuminate\Support\Str;

class MigrationGenerator extends BaseGenerator
{
    public function generate(): void
    {
        $table      = Str::snake($this->context->moduleName);
        $className = 'Create' . $this->context->moduleName . 'Table';
        $fileName   = date('Y_m_d_His')."_create_{$table}_table.php";
        $path       = database_path("migrations/{$fileName}");

        $existing = glob(database_path("migration/*_create{$table}_table.php"));

        if(!empty($existing)){
            $this->command->warn("Migration for '{$table}' already exists, skipping");
        }

        $this->command->info("Creating migration {$fileName}");

        $stubEntry = $this->config['stubs'][0] ?? ['stub' => 'migration.stub'];
        $this->createFile($path, $this->context->stubPath($stubEntry['stub']),['table'=> $table, 'class' => $className]);
    }
}
