<?php

namespace Henrygodev\LaravelModule\Tests;

use Henrygodev\LaravelModule\LaravelModuleServiceProvider;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;
use Override;

class MakeModuleCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::deleteDirectory(app_path('Modules'));
        File::deleteDirectory(app_path('Domain'));

        // Limpiar migraciones generadas por tests anteriores
        foreach (glob(database_path('migrations/*_create_*_table.php')) as $file) {
            unlink($file);
        }
    }

    protected function getPackageProviders($app): array
    {
        return [LaravelModuleServiceProvider::class];
    }
    
    /**
     * @test
    */
    public function it_create_all_module_files():void
    {
        // Verificar que se ejecute
        $this->artisan("make:module", ['name'=>'Product'])->assertSuccessful();
        // Verificar archivos creados
        $this->assertFileExists(app_path('Modules/Products/Models/Product.php'));
        $this->assertFileExists(app_path('Modules/Products/Controllers/ProductController.php'));
        $this->assertFileExists(app_path('Modules/Products/Requests/StoreProductRequest.php'));
        $this->assertFileExists(app_path('Modules/Products/Requests/UpdateProductRequest.php'));
    }
    
    /**
     * @test
    */
    public function it_creates_the_correct_directories():void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->assertDirectoryExists(app_path('Modules/Products'));
        $this->assertDirectoryExists(app_path('Modules/Products/Controllers'));
        $this->assertDirectoryExists(app_path('Modules/Products/Models'));
        $this->assertDirectoryExists(app_path('Modules/Products/Requests'));
    }

    /**
     * @test
    */
    public function it_pluralizes_the_module_name():void
    {
        $this->artisan('make:module', ['name' => 'Category'])->assertSuccessful();
        $this->assertDirectoryExists(app_path('Modules/Categories'));
        $this->assertFileExists(app_path('Modules/Categories/Models/Category.php'));
    }

    /**
     * @test
    */
    public function it_normalizes_input_to_studly_case():void
    {
        $this->artisan('make:module', ['name' => 'product_category'])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/ProductCategories/Models/ProductCategory.php'));
    }


    /**
     * --- CONTENIDO DE ARCHIVOS GENERADOS
    */


    /**
     * @test
    */
    public function model_has_correct_namespace_and_class():void
    {
        $this->artisan('make:module', ['name' => "Product"])->assertSuccessful();

        $content = file_get_contents(app_path("Modules/Products/Models/Product.php"));

        $this->assertStringContainsString("namespace App\Modules\Products\Models",  $content);
        $this->assertStringContainsString("class Product",  $content);
    }

    /**
     * @test
    */
    public function store_request_has_correct_namespace_and_class():void
    {
        $this->artisan("make:module", ['name' => "Product"])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Requests/StoreProductRequest.php'));

        $this->assertStringContainsString('namespace App\Modules\Products\Requests', $content);
        $this->assertStringContainsString('class StoreProductRequest', $content);
    }

    /**
     * @test
    */
    public function controller_has_correct_namespace_and_class(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('namespace App\Modules\Products\Controllers', $content);
        $this->assertStringContainsString('class ProductController', $content);
    }

    // Opciones api y resource

    /**
     * @test
    */
    public function api_controller_contains_json_response(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--api' =>true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('StoreProductRequest', $content);
        $this->assertStringContainsString('UpdateProductRequest', $content);
        $this->assertStringContainsString('public function store', $content);
        $this->assertStringContainsString('public function update', $content);
    }

    /**
     * @test
    */
    public function resource_controller_contains_view_returns(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--resource' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('function index', $content);
        $this->assertStringContainsString('function create', $content);
        $this->assertStringContainsString('function store', $content);
        $this->assertStringContainsString('function show', $content);
        $this->assertStringContainsString('function edit', $content);
        $this->assertStringContainsString('function update', $content);
        $this->assertStringContainsString('function destroy', $content);
    }

    /**
     * @test
    */
    public function plain_controller_has_no_methods(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));
        $this->assertStringNotContainsString('JsonResponse', $content);
        $this->assertStringNotContainsString('view(', $content);
    }

    // Archivos existentes

    /**
     * @test
    */
    public function it_skips_existing_file_without_falling(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
    }

    // Rollback

    /**
     * @test
    */
    public function it_cleans_up_directories_after_failed_generation(): void
    {
        $this->artisan('make:module', ['name' => '123Bad'])->assertFailed();

        $this->assertDirectoryDoesNotExist(app_path('Modules/123Bad'));
    }

    /**
     * @test
    */
    public function it_fails_with_a_numeric_name(): void
    {
        $this->artisan('make:module', ['name' => '123'])->assertFailed();
    }

    /**
     * @test
    */
    public function it_fails_with_special_characters(): void
    {
        $this->artisan('make:module', ['name' => 'Product!'])->assertFailed();
    }

    // Stubs

    /**
     * @test
    */
    public function it_uses_published_stub_over_package_default():void
    {
        // Publicar un stub
        $publishedDir = base_path("stubs/laravel-module");

        if (! is_dir($publishedDir)) {
            mkdir($publishedDir, 0755, true);
        }

        file_put_contents("{$publishedDir}/model.stub", "<?php\n// CUSTOM STUB\nnamespace {{namespace}};\nclass {{class}} {}");

        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Models/Product.php'));
        $this->assertStringContainsString('// CUSTOM STUB', $content);

        unlink("{$publishedDir}/model.stub");
        rmdir($publishedDir);
    }

    // Migracion

    /**
     * @test
    */
    public function it_migration_is_not_generated_without_flag():void
    {
        $this->artisan('make:module', ['name'=> 'Product'])->assertSuccessful();

        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertEmpty($migrations);
    }

    /**
     * @test
     */
    public function it_generates_migration_with_flag():void
    {
        $this->artisan('make:module', ['name'=> 'Product', '--migration' => true])->assertSuccessful();
        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertNotEmpty($migrations);

        foreach ($migrations as $file) {
            unlink($file);
        }
    }

    /**@test
     *
    */
    public function it_migration_contains_correct_table_name():void
    {
        $this->artisan('make:module', ['name'=> 'Product', '--migration' => true])->assertSuccessful();
        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $content = file_get_contents($migrations[0]);
 
        $this->assertStringContainsString('class CreateProductsTable extends Migration', $content);
        $this->assertStringContainsString("Schema::create('products'", $content);
        $this->assertStringContainsString("Schema::dropIfExists('products'", $content);
        
        foreach ($migrations as $file) {
            unlink($file);
        }
    }

    /**
     * @test
     */
    public function it_migration_uses_plural_snake_case_table_name():void
    {
        $this->artisan('make:module', ['name'=> 'Product', '--migration' => true])->assertSuccessful();
        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertNotEmpty($migrations);

        $content = file_get_contents($migrations[0]);
        $this->assertStringContainsString('CreateProductsTable', $content);

        foreach ($migrations as $file) {
            unlink($file);
        }
    }

    public function test_it_skips_duplicate_migration(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '-m' => true])->assertSuccessful();
        $this->artisan('make:module', ['name' => 'Product', '-m' => true])->assertSuccessful();
 
        $migrations = glob(database_path('migrations/*_create_products_table.php'));
        $this->assertCount(1, $migrations);
    }
 
    
    /*
    |--------------------------------------------------------------------------
    | Configuración
    |--------------------------------------------------------------------------
    */
 
    public function test_it_uses_custom_modules_path_from_config(): void
    {
        config(['laranest.modules_path' => 'Domain']);
 
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
 
        $this->assertDirectoryExists(app_path('Domain/Products'));
        $this->assertFileExists(app_path('Domain/Products/Models/Product.php'));
    }
 
    public function test_it_uses_custom_namespace_from_config(): void
    {
        config(['laranest.modules_namespace' => 'App\\Domain']);
 
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
 
        $content = file_get_contents(app_path('Modules/Products/Models/Product.php'));
        $this->assertStringContainsString('namespace App\Domain\Products\Models;', $content);
    }
 
    public function test_it_uses_custom_structure_from_config(): void
    {
        config([
            'laranest.structure' => [
                'models' => [
                    'path'      => 'Domain/Entities',
                    'namespace' => 'Domain/Entities',
                    'generator' => \Henrygodev\LaravelModule\Generators\ModelGenerator::class,
                    'stubs'     => [
                        ['stub' => 'model.stub', 'prefix' => null, 'suffix' => null],
                    ],
                ],
            ],
        ]);
 
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
 
        $this->assertFileExists(app_path('Modules/Products/Domain/Entities/Product.php'));
 
        $content = file_get_contents(app_path('Modules/Products/Domain/Entities/Product.php'));
        $this->assertStringContainsString('namespace App\Modules\Products\Domain\Entities;', $content);
    }
 
    public function test_custom_generator_in_structure_is_used(): void
    {
        config([
            'laranest.structure.models' => [
                'path'      => 'Models',
                'namespace' => 'Models',
                'generator' => \Henrygodev\LaravelModule\Generators\ModelGenerator::class,
                'stubs'     => [
                    ['stub' => 'model.stub', 'prefix' => null, 'suffix' => null],
                    ['stub' => 'model.stub', 'prefix' => null, 'suffix' => 'Interface'],
                ],
            ],
        ]);
 
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
 
        $this->assertFileExists(app_path('Modules/Products/Models/Product.php'));
        $this->assertFileExists(app_path('Modules/Products/Models/ProductInterface.php'));
    }
}
