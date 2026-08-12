<?php

namespace Henrygodev\LaravelModule\Tests;

class MigrationGeneratorTest extends TestCase
{
    protected function tearDown(): void
    {
        // Limpiar migraciones generadas antes del tearDown del padre
        foreach (glob(database_path('migrations/*_create_*_table.php')) as $file) {
            unlink($file);
        }

        parent::tearDown();
    }

    /*
    |--------------------------------------------------------------------------
    | Flag --migration
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function migration_is_not_generated_without_flag(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertEmpty($migrations);
    }

    /** @test */
    public function it_generates_migration_with_flag(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--migration' => true])->assertSuccessful();

        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertNotEmpty($migrations);
    }

    /*
    |--------------------------------------------------------------------------
    | Contenido de la migración
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function migration_contains_correct_class_and_table_name(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--migration' => true])->assertSuccessful();

        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $content = file_get_contents($migrations[0]);

        $this->assertStringContainsString('class CreateProductsTable extends Migration', $content);
        $this->assertStringContainsString("Schema::create('products'", $content);
        $this->assertStringContainsString("Schema::dropIfExists('products'", $content);
    }

    /** @test */
    public function migration_uses_plural_snake_case_table_name(): void
    {
        $this->artisan('make:module', ['name' => 'ProductCategory', '--migration' => true])->assertSuccessful();

        $migrations = glob(database_path('migrations/*_create_product_categories_table.php'));
        $this->assertNotEmpty($migrations);

        $content = file_get_contents($migrations[0]);
        $this->assertStringContainsString('CreateProductCategoriesTable', $content);
        $this->assertStringContainsString("Schema::create('product_categories'", $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Duplicados
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_skips_duplicate_migration(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--migration' => true])->assertSuccessful();
        $this->artisan('make:module', ['name' => 'Product', '--migration' => true])->assertSuccessful();

        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertCount(1, $migrations);
    }
}
