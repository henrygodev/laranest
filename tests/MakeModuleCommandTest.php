<?php

namespace Henrygodev\LaravelModule\Tests;

use Henrygodev\LaravelModule\Generators\ModelGenerator;
use Illuminate\Support\Facades\File;

class MakeModuleCommandTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Estructura y directorios
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_creates_all_module_files(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/Products/Models/Product.php'));
        $this->assertFileExists(app_path('Modules/Products/Controllers/ProductController.php'));
        $this->assertFileExists(app_path('Modules/Products/Requests/StoreProductRequest.php'));
        $this->assertFileExists(app_path('Modules/Products/Requests/UpdateProductRequest.php'));
    }

    /** @test */
    public function it_creates_the_correct_directories(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->assertDirectoryExists(app_path('Modules/Products'));
        $this->assertDirectoryExists(app_path('Modules/Products/Controllers'));
        $this->assertDirectoryExists(app_path('Modules/Products/Models'));
        $this->assertDirectoryExists(app_path('Modules/Products/Requests'));
    }

    /** @test */
    public function it_pluralizes_the_module_name(): void
    {
        $this->artisan('make:module', ['name' => 'Category'])->assertSuccessful();

        $this->assertDirectoryExists(app_path('Modules/Categories'));
        $this->assertFileExists(app_path('Modules/Categories/Models/Category.php'));
    }

    /** @test */
    public function it_normalizes_input_to_studly_case(): void
    {
        $this->artisan('make:module', ['name' => 'product_category'])->assertSuccessful();

        $this->assertFileExists(app_path('Modules/ProductCategories/Models/ProductCategory.php'));
    }

    /*
    |--------------------------------------------------------------------------
    | Contenido de archivos generados
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function model_has_correct_namespace_and_class(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Models/Product.php'));

        $this->assertStringContainsString('namespace App\Modules\Products\Models', $content);
        $this->assertStringContainsString('class Product', $content);
    }

    /** @test */
    public function store_request_has_correct_namespace_and_class(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Requests/StoreProductRequest.php'));

        $this->assertStringContainsString('namespace App\Modules\Products\Requests', $content);
        $this->assertStringContainsString('class StoreProductRequest', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Archivos existentes
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_skips_existing_file_without_failing(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();
    }

    /*
    |--------------------------------------------------------------------------
    | Validación de nombres
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_fails_with_a_numeric_name(): void
    {
        $this->artisan('make:module', ['name' => '123'])->assertFailed();
    }

    /** @test */
    public function it_fails_with_special_characters(): void
    {
        $this->artisan('make:module', ['name' => 'Product!'])->assertFailed();
    }

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_cleans_up_directories_after_failed_generation(): void
    {
        $this->artisan('make:module', ['name' => '123Bad'])->assertFailed();

        $this->assertDirectoryDoesNotExist(app_path('Modules/123Bad'));
    }

    /*
    |--------------------------------------------------------------------------
    | Stubs publicados
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_uses_published_stub_over_package_default(): void
    {
        $publishedDir = base_path('stubs/laravel-module');

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

    /*
    |--------------------------------------------------------------------------
    | Configuración personalizada
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function it_uses_custom_modules_path_from_config(): void
    {
        config(['laranest.modules_path' => 'Domain']);

        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $this->assertDirectoryExists(app_path('Domain/Products'));
        $this->assertFileExists(app_path('Domain/Products/Models/Product.php'));
    }

    /** @test */
    public function it_uses_custom_namespace_from_config(): void
    {
        config(['laranest.modules_namespace' => 'App\\Domain']);

        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Models/Product.php'));
        $this->assertStringContainsString('namespace App\Domain\Products\Models;', $content);
    }

    /** @test */
    public function it_uses_custom_structure_from_config(): void
    {
        config([
            'laranest.structure' => [
                'models' => [
                    'path'      => 'Domain/Entities',
                    'namespace' => 'Domain/Entities',
                    'generator' => ModelGenerator::class,
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

    /** @test */
    public function it_uses_multiple_stubs_from_custom_config(): void
    {
        config([
            'laranest.structure.models' => [
                'path'      => 'Models',
                'namespace' => 'Models',
                'generator' => ModelGenerator::class,
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
