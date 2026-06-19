# Laranest - Laravel with modules
A simple scaffolding package inspired by NestJs architecture. Each module encapsulates its own controller, requests, and model - keeping your application organized and scalable.

## Requirements

- PHP ^7.4 | ^8.0
- Laravel ^8.0 | ^9.0 | ^10.0 | ^11.0 | ^12.0

# Install
To install via Composer, run

```bash
composer require henrygodev/laranest
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

### Migrations
Generate a migration file in `database/migrations`

```bash
php artisan make:module Product --migration
# or
php artisan make:module Product -m
```

Generates:

```
database/
    └── migrations/
        └── 2025_01_01_000000_create_products_table.php
```

Also combinable with other flags:
```bash
php artisan make:module Product --api --migration
php artisan make:module Product --resource -m
```

Running the command twice with `-m` will skip the migration if one already exists for that table.

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

## Configuration

Publish the config file to customize the package behavior:

```bash
php artisan vendor:publish --tag=laravel-module-config
```

This creates `config/laranest.php` in your project:

```php
return [
    'modules_path'      => 'Modules',       // app/Modules/
    'modules_namespace' => 'App\\Modules',

    'structure' => [
        'models' => [
            'path'      => 'Models',
            'namespace' => 'Models',
            'generator' => \Henrygodev\LaravelModule\Generators\ModelGenerator::class,
            'stubs'     => [
                ['stub' => 'model.stub', 'prefix' => null, 'suffix' => null],
            ],
        ],
        // controllers, requests...
    ],
];
```

### Changing the modules folder

```php
'modules_path'      => 'Domain',
'modules_namespace' => 'App\\Domain',
```

All modules will now generate under `app/Domain/`.

### Custom structure

Each entry in `structure` defines what gets generated and how. You control the folder, namespace, generator class, and stubs:

```php
'structure' => [
    'models' => [
        'path'      => 'Domain/Entities',   // app/Modules/Products/Domain/Entities/
        'namespace' => 'Domain/Entities',   // App\Modules\Products\Domain\Entities
        'generator' => \Henrygodev\LaravelModule\Generators\ModelGenerator::class,
        'stubs'     => [
            ['stub' => 'model.stub', 'prefix' => null, 'suffix' => null],
        ],
    ],

    'requests' => [
        'path'      => 'Http/Requests',
        'namespace' => 'Http/Requests',
        'generator' => \Henrygodev\LaravelModule\Generators\RequestGenerator::class,
        'stubs'     => [
            ['stub' => 'store-request.stub',  'prefix' => 'Store',  'suffix' => 'Request'],
            ['stub' => 'update-request.stub', 'prefix' => 'Update', 'suffix' => 'Request'],
            ['stub' => 'index-request.stub',  'prefix' => 'Index',  'suffix' => 'Request'], // custom
        ],
    ],
],
```

### Custom generators 

You can register your own generator class for any entry. The generator must extend `BaseGenerator` and implement `generate()`:

```php
// app/Generators/RepositoryGenerator.php
class RepositoryGenerator extends \Henrygodev\LaravelModule\Generators\BaseGenerator
{
    public function generate(): void
    {
        $this->command->info("Creating repository {$this->context->name}");
        $this->generateFromConfig();
    }
}
```

Then declare it in your config:
```php
'structure' => [
    // ...existing entries...

    'repositories' => [
        'path'      => 'Repositories',
        'namespace' => 'Repositories',
        'generator' => \App\Generators\RepositoryGenerator::class,
        'stubs'     => [
            ['stub' => 'repository.stub', 'prefix' => null, 'suffix' => 'Repository'],
        ],
    ],
],
```

From now on, every `make:module` will also generate a repository - without touching the package code.

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
    ├── migration.stub
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
