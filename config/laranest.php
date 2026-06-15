<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    |
    | The directory where modules will be generated, relative to the app/ folder.
    | For example, 'Modules' generates files under app/Modules/,
    | while 'Domain' generates files under app/Domain/.
    |
    */
    'modules_path' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Modules Namespace
    |--------------------------------------------------------------------------
    |
    | The base namespace for all generated modules.
    | This should match your composer.json autoload configuration.
    |
    */
    'modules_namespace' => 'App\\Modules',

    
    /*
    |--------------------------------------------------------------------------
    | Migration
    |--------------------------------------------------------------------------
    |
    | Controls the stub used when --migration flag is passed.
    | The migration is generated in database/migrations/ with a timestamp prefix.
    |
    */
    'migration' => [
        'generator' => \Henrygodev\LaravelModule\Generators\MigrationGenerator::class,
        'stubs' => [
            [
                'stub' => 'migration.stub'
            ]
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Module Structure
    |--------------------------------------------------------------------------
    |
    | Defines what gets generated for each module and how.
    | Each entry declares a generator class, the folder path, namespace segment,
    | and one or more stubs to render.
    |
    | Each stub entry supports:
    |   - stub:    filename inside stubs/laravel-module/ (or package defaults)
    |   - prefix:  prepended to the module name for the class name (optional)
    |   - suffix:  appended to the module name for the class name (optional)
    |
    | Example class name resolution:
    |   module = Product, prefix = Store, suffix = Request -> StoreProductRequest
    |   module = Product, prefix = null,  suffix = Controller -> ProductController
    |   module = Product, prefix = null,  suffix = null -> Product
    |
    */
    'structure' => [

        'models' => [
            'path'      => 'Models',
            'namespace' => 'Models',
            'generator' => \Henrygodev\LaravelModule\Generators\ModelGenerator::class,
            'stubs'     => [
                [
                    'stub'   => 'model.stub',
                    'prefix' => null,
                    'suffix' => null,
                ],
            ],
        ],

        'controllers' => [
            'path'      => 'Controllers',
            'namespace' => 'Controllers',
            'generator' => \Henrygodev\LaravelModule\Generators\ControllerGenerator::class,
            'stubs'     => [
                [
                    'type'          => 'plain',
                    'stub'          => 'controller.stub',
                    'prefix'        => null,
                    'suffix'        => 'Controller',
                    'imports_stub'  => null,
                    'methods_stub'  => null,
                ],
                [
                    'type'          => 'api',
                    'stub'          => 'controller.stub',
                    'prefix'        => null,
                    'suffix'        => 'Controller',
                    'imports_stub'  => 'controller-api-imports.stub',
                    'methods_stub'  => 'controller-api-methods.stub',
                ],
                [
                    'type'          => 'resource',
                    'stub'          => 'controller.stub',
                    'prefix'        => null,
                    'suffix'        => 'Controller',
                    'imports_stub'  => 'controller-api-imports.stub',
                    'methods_stub'  => 'controller-resource-methods.stub',
                ]
            ],
        ],

        'requests' => [
            'path'      => 'Requests',
            'namespace' => 'Requests',
            'generator' => \Henrygodev\LaravelModule\Generators\RequestGenerator::class,
            'stubs'     => [
                [
                    'stub'   => 'store-request.stub',
                    'prefix' => 'Store',
                    'suffix' => 'Request',
                ],
                [
                    'stub'   => 'update-request.stub',
                    'prefix' => 'Update',
                    'suffix' => 'Request',
                ],
            ],
        ],
    ],
];