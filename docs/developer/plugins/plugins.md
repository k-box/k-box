# K-Box Plugin system

> **This is an highly experimental feature** and is protected by a the [`plugins` feature flag](../flags.md)


## What is a plugin

A plugin delivers customizations to the K-Box without the requirement to have a custom build (or fork) of the codebase.

As of now a K-Box plugin is highly inspired by the [Laravel Packages](https://laravel.com/docs/5.5/packages) approach, 
in fact, as described in the [limitations](#limitations), it is loaded like a Laravel Package with the help of Composer.

## How to create a plugin

A plugin is a library that has a specific `composer.json` that define its dependencies as well 
as some additional entries to be recognized as a plugin.

In this library a class that extends `KBox\Plugins\Plugin` must be defined. This class will be the 
entry point of the plugin as the Service Providers are the entry point of a Laravel Package.

### composer.json

The `composer.json` file is used to define the plugin metadata and, from the K-Box perspective, to 
recognize plugins from libraries.

To do so the `type` attribute must be set to `kbox-plugin`.

Instead of requiring users to manually add your plugin service provider to the list, you may define the 
provider in the extra section of your package's `composer.json` file:

```json
"extra": {
    "kbox": {
        "providers": [
            "MyPlugin\\Example\\ExamplePlugin"
        ]
    }
},
```

Once your package has been configured for discovery, the K-Box will automatically register its service provider when it is installed, creating a convenient installation experience for your package's users.

A complete `composer.json` for a K-Box plugin is in the next code block.

```json
{
    "name": "k-box/demo-plugin",
    "type": "kbox-plugin",
    "description": "Demo K-Box plugin",
    "keywords": ["k-box", "k-link", "demo", "plugin"],
    "license": "MIT",

    "authors": [
        {
            "name": "Alessio Vertemati",
            "email": "alessio@oneofftech.xyz"
        }
    ],
    "require": {
        "php": ">=7.1"
    },
    "autoload": {
        "psr-4": {
            "MyPlugin\\Example\\": ""
        }
    },
    "extra": {
        "branch-alias": {
            "dev-master": "0.1-dev"
        },
        "kbox": {
            "providers": [
                "MyPlugin\\Example\\ExamplePlugin"
            ]
        }
    }
}
```

### Plugin class

A plugin must have a plugin service provider. The service provider is the integration point between the 
plugin offered services and the K-Box. Once instantiated the class will have access to the 
K-Box application for registering views, controllers, language files,...

```php
<?php

namespace MyPlugin\Example;

use KBox\Plugins\Plugin;

class ExamplePlugin extends Plugin
{
    public function register()
    {
        // ...
    }

    public function boot()
    {
        // ...
    }
}
```

#### Digging Deeper into Plugins

- [Define Plugin Settings](./settings.md)
- [Listening application events from Plugins](./events.md)

### Thumbnail generators

A plugin can register a custom thumbnail generator using the `registerThumbnailGenerator` method

```php

use MyPlugin\Thumbnail\ImageThumbnailGenerator;

public function boot()
{
    $this->registerThumbnailGenerator(ImageThumbnailGenerator::class);
}
```

## Plugin discovery

The Plugin discovery is not automate. To let the K-Box know of the presence of a plugin please run the following command

```bash
php artisan plugin:discovery
```

The list of discovered plugins will be presented in the _Administration > Plugins_ section.

## Enable/Disable a Plugin

Go to  _Administration > Plugins_ and press "Enable" or "Disable" near the discovered plugins.

## Limitations

In the current implementation **class autoloading is not available**. To ensure that the plugin classes
and dependencies are properly resolved, add a [folder package](https://getcomposer.org/doc/05-repositories.md#path)
in the K-Box `composer.json` and reference the plugin, by package name, in the `required` section.

Once complete execute `composer update {packageName}`, e.g. `composer update k-box/example-plugin`.

The next code blocks assumes that the package is located in `./plugins/example` (from the root of the 
repository) and is named `k-box/example-plugin`.

```json
"repositories": [
    {
        "type": "path",
        "url": "plugins/example",
        "options": {
            "symlink": true
        }
    }
]
```

```json
"require": {
    "k-box/example-plugin":"*"
}
```



## Implementation details

- Discovered plugins are stored in a file inside the `storage/app` directory
- Enabled plugins are stored in a file inside the `storage/app` directory

