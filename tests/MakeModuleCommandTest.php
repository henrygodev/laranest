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
        mkdir($publishedDir, 0755, true);

        file_put_contents("{$publishedDir}/model.stub", "<?php\n// CUSTOM STUB\nnamespace {{namespace}};\nclass {{class}} {}");

        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Models/Product.php'));
        $this->assertStringContainsString('// CUSTOM STUB', $content);

        unlink("{$publishedDir}/model.stub");
        rmdir($publishedDir);
    }
}
