<?php

namespace Henrygodev\LaravelModule\Tests;

class ControllerGeneratorTest extends TestCase
{
    /*
    |--------------------------------------------------------------------------
    | Namespace y clase
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function controller_has_correct_namespace_and_class(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('namespace App\Modules\Products\Controllers', $content);
        $this->assertStringContainsString('class ProductController', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Controller plain (sin flags)
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function plain_controller_has_no_methods_or_imports(): void
    {
        $this->artisan('make:module', ['name' => 'Product'])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringNotContainsString('JsonResponse', $content);
        $this->assertStringNotContainsString('view(', $content);
        $this->assertStringNotContainsString('public function index', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Controller --api
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function api_controller_contains_json_response_methods(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--api' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('JsonResponse', $content);
        $this->assertStringContainsString('public function index', $content);
        $this->assertStringContainsString('public function store', $content);
        $this->assertStringContainsString('public function show', $content);
        $this->assertStringContainsString('public function update', $content);
        $this->assertStringContainsString('public function destroy', $content);
    }

    /** @test */
    public function api_controller_imports_requests(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--api' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('StoreProductRequest', $content);
        $this->assertStringContainsString('UpdateProductRequest', $content);
    }

    /** @test */
    public function api_controller_injects_service_when_service_flag_is_passed(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--api' => true, '--service' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('ProductService', $content);
        $this->assertStringContainsString('__construct', $content);
    }

    /** @test */
    public function api_controller_does_not_import_model_directly(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--api' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        // El controller no debe usar el model directamente, solo a través del service
        $this->assertStringNotContainsString('use App\Modules\Products\Models\Product;', $content);
    }

    /*
    |--------------------------------------------------------------------------
    | Controller --resource
    |--------------------------------------------------------------------------
    */

    /** @test */
    public function resource_controller_contains_all_seven_methods(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--resource' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('public function index', $content);
        $this->assertStringContainsString('public function create', $content);
        $this->assertStringContainsString('public function store', $content);
        $this->assertStringContainsString('public function show', $content);
        $this->assertStringContainsString('public function edit', $content);
        $this->assertStringContainsString('public function update', $content);
        $this->assertStringContainsString('public function destroy', $content);
    }

    /** @test */
    public function resource_controller_injects_service_when_service_flag_is_passed(): void
    {
        $this->artisan('make:module', ['name' => 'Product', '--resource' => true, '--service' => true])->assertSuccessful();

        $content = file_get_contents(app_path('Modules/Products/Controllers/ProductController.php'));

        $this->assertStringContainsString('ProductService', $content);
        $this->assertStringContainsString('__construct', $content);
    }
}
