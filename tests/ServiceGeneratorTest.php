<?php

namespace Henrygodev\LaravelModule\Tests;

class ServiceGeneratorTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Flag --service
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function service_is_not_generated_without_flag(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->assertFileDoesNotExist(app_path('Modules/Products/Services/ProductService.php'));
    }

    /** @test */
    public function service_directory_is_not_created_without_flag(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->assertDirectoryDoesNotExist(app_path('Modules/Products/Services'));
    }

    /** @test */
    public function it_generates_service_with_flag(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/Products/Services/ProductService.php'));
    }

    /** @test */
    public function it_creates_services_directory_with_flag(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        $this->assertDirectoryExists(app_path('Modules/Products/Services'));
    }

    /** @test */
    public function service_shorthand_flag_works(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '-s' => true])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/Products/Services/ProductService.php'));
    }

    /*
    |--------------------------------------------------------------------------
    | Namespace y clase
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function service_has_correct_namespace_and_class(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Services/ProductService.php'));

        $this->assertStringContainsString('namespace App\Modules\Products\Services', $content);
        $this->assertStringContainsString('class ProductService', $content);
    }

    /** @test */
    public function service_uses_correct_model_namespace(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Services/ProductService.php'));

        $this->assertStringContainsString('use App\Modules\Products\Models\Product;', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Contenido del servicio
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function service_contains_crud_methods(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Services/ProductService.php'));

        $this->assertStringContainsString('public function getAll', $content);
        $this->assertStringContainsString('public function findById', $content);
        $this->assertStringContainsString('public function create', $content);
        $this->assertStringContainsString('public function update', $content);
        $this->assertStringContainsString('public function delete', $content);
    }

    /** @test */
    public function service_references_model_class_in_methods(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Services/ProductService.php'));

        $this->assertStringContainsString('Product::', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Nombre compuesto
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function service_name_is_correct_for_compound_module_name(): void
    {
        $this->artisan('make:module', ['name' => 'ProductCategory', '--service' => true])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/ProductCategories/Services/ProductCategoryService.php'));

        $content = file_get_contents(app_path('Modules/ProductCategories/Services/ProductCategoryService.php'));

        $this->assertStringContainsString('class ProductCategoryService', $content);
        $this->assertStringContainsString('namespace App\Modules\ProductCategories\Services', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Combinación con otros flags
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function service_generates_alongside_api_controller(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--api' => true, '--service' => true])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/Products/Controllers/ProductController.php'));
        $this->assertFileExists(app_path('Modules/Products/Services/ProductService.php'));
    }

    /** @test */
    public function service_generates_alongside_migration(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true, '--migration' => true])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/Products/Services/ProductService.php'));

        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertNotEmpty($migrations);

        foreach ($migrations as $file) {
            unlink($file);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Archivos existentes
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_skips_existing_service_without_failing(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();
        $this->artisan('make:module', ['name' => 'Product', '--service' => true])->assertSuccessful();

        // El archivo debe existir y no estar corrupto
        $content = file_get_contents(app_path('Modules/Products/Services/ProductService.php'));
        $this->assertStringContainsString('class ProductService', $content);
    }
}
