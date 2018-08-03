# Plugin Settings

A Plugin can define a settings route. That route can be used to let the administrator configure the specific settings of the plugin.

The route must be named according to the following format:

```
plugins.{plugin_name}.settings
```

where `plugin_name` is the slug version of the package name written in the `composer.json`'s `name` attribute, e.g. `k-box/example-plugin => k-box-example-plugin`.