# Laranest - Laravel with modules
A simple scaffolding package inspired by NestJs architecture. Each module encapsulates its own controller, requests, and model - keeping your application organized and scalable.

## Requirements

- PHP ^7.4 | ^8.0
- Laravel ^8.0 | ^9.0 | ^10.0 | ^11.0 | ^12.0

# Install
To install via Composer, run
```bash
composer require henrygodev/laravel-module
```

The package is auto-discovery by Laravel - no need to register the service provider manually.

## Usage

### Basic module
```bash
php artisan make:module Product
```

Generates:
```
app/
└── Modules/
    └── Products/
        ├── Controllers/
        │   └── ProductController.php
        ├── Models/
        │   └── Product.php
        └── Requests/
            ├── StoreProductRequest.php
            └── UpdateProductRequest.php
```

### API module
Generate a controller with JSON response and full CRUD methods.

```bash
php artisan make:module Product --api
```


```php
class ProductController extends Controller
{
    public function index(): JsonResponse { ... }
    public function store(StoreProductRequest $request): JsonResponse { ... }
    public function show(Product $product): JsonResponse { ... }
    public function update(UpdateProductRequest $request, Product $product): JsonResponse { ... }
    public function destroy(Product $product): JsonResponse { ... }
}
```

### Resource module
Generate a controller with view returns and redirects, following Laravel's resource convention.

```bash
php artisan make:module Product --resource
```

```php
class ProductController extends Controller
{
    public function index() { ... }
    public function create() { ... }
    public function store(StoreProductRequest $request) { ... }
    public function show(Product $model) { ... }
    public function edit(Product $model) { ... }
    public function update(UpdateProductRequest $request, Product $model) { ... }
    public function destroy(Product $model) { ... }
}
```

### Multi-word names

The module name is automatically converted to StudlyCase and pluralized.

```bash
php artisan make:module ProductCategory
# or
php artisan make:module product_category
```

Both generate:
```
app/Modules/ProductCategories/
```

## Customizing stubs

Publish the defaults stubs to your project:

```bash
php artisan vendor:publish --tag=larvel-module-stubs
```

This copies all stubs to:

```
stubs/
└── laravel-module/
    ├── controller.stub
    ├── controller-api-imports.stub
    ├── controller-api-methods.stub
    ├── controller-resource-methods.stub
    ├── model.stub
    ├── store-request.stub
    └── update-request.stub
```

Edit any stub to match your project conventions. Published stubs take priority over the package defaults - you only need to publish the ones you want to customize.

**Example:** adding `SoftDelete` to every generated model:


```php
// stubs/laravel-module/model.stub
<?php
 
namespace {{ namespace }};
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class {{ class }} extends Model
{
    use SoftDeletes;
 
    protected $guarded = [];
}
```

From that point on, every module you generate will include `SoftDeletes` automatically.

## Rollback on failure

If any file fails to generate, the package automatically removes all files an directories created during that run, leaving your project clean.

## License

MIT