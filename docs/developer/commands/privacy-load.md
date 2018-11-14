# `privacy:load` command

Create the privacy policy and term page from the templates.
The command is aimed to load and create a privacy policy starting from a defined template.

```
$ php artisan privacy:load [options]
```

> The command will load/create the privacy policy only if different from the currently defined privacy policy

## Templates

This command will attempt to load and create pages from the following templates

- `privacy-legal.{app.locale}.md`
- `privacy-summary.{app.locale}.md`

where `{app.locale}` is replaced by the currently configured application locale (read from the configuration setting `app.locale`)

> Templates can be found in `/resources/assets/pages/stub`
