# K-Box Flags

Feature Flags allow to create experimental features that live in the codebase, but are not enabled by default for all.

Flag status is stored in the database.


## Available flags

- `plugins`: this feature is about a [plugin system](./plugins/plugins.md)

## Enable a feature flag

Flags can be enabled at runtime using the `flags` command. Once enabled a flag state is persisted in the database.

```
php artisan flags [--enable] {flag_name}
```

In addition, if you are using the K-Box Docker image, flags can be enabled using the `KBOX_FLAGS` environment variable.
The variable accepts flag names separated using a space character.

## Disable a feature flag

Flags can be disabled using the `flags` command.

```
php artisan flags --disable {flag_name}
```

## Check for a feature flag status

There are different ways for checking if a feature flag is enabled.

```php
// helper function. Passing a flag name will return true if enabled
flags(Flag::PLUGINS);

// flags methods. Each flag has a corresponding isFlagEnabled method
// the flag name must be capitalized
flags()->isPluginsEnabled();

// or using the isEnabled($flag) method
flags()->isEnabled('plugins');
```

In a Blade template file, you can use the `@flag` if statement

```php
@flag('plugins')

    // The flag is enabled...

@endflag
```

## Add a new flag

Defining a new flag requires you to add a constant with a string value 
in the `KBox\Flags` class (`app/Flags.php`). The constant must be written in 
upper case and its value cannot contain spaces.

```php
class Flags
{
    const PLUGINS = 'plugins';

    // ...
}
```
