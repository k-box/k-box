# K-Box Command Line Tools

> The usage of the command line options requires direct access to the running 
> K-Box instance.


The K-Box command line suite of commands rely on the [Laravel Artisan CLI](https://laravel.com/docs/7.x/artisan).

Here are only listed the specific commands added by the K-Box:

- [`create-admin`](./create-admin.md): Create a K-Box Administrator
- [`dms:update`](./update-command.md): Perform the installation/update steps for the 
  K-Box.
- [`dms:reindex`](./reindex-command.md): Perform the reindexing of the currently 
  indexed documents.
- `dms:test`: Tests the configuration and connection to the private K-Link Core.
- `dms:sessions`: Get the user's session status.
- `dms:queuelisten`: Start listening for asynchronous jobs on the queue and report the status 
  to the admin interface.
- `dms:sync`: Performs a synchronization of the documents from the DMS that do not 
  exists on the Core.
- `dms:lang-publish`: Publish Javascript language files for RequireJS i18n plugin
- [`users:import`](./user-import-command.md): Import users from a CSV file
- [`documents:check-latest-version`](./documents-check-latest-version.md): Check if 
  the latest version details of a Document are correctly reported in a document descriptor
- `collections:clean-duplicates`: Clean the duplicated documents contained in a collection
- `collections:list`: Performs actions on collections
- [`lang:check`](./lang-check.md) Get the status of the localization in different languages
- [`files:orphans`](./files-orphans.md): Find and remove orphan files, i.e. the one not linked
  to a document descriptor
- `video:elaborate`: Trigger the video elaboration for the specified documents
- [`statistics`](./statistics-command.md): Generate some usage statistics of the K-Box
- `flags`: enable or disable an experimental feature. See [K-Box Flags](../flags.md)
- [`privacy:load`](./privacy-load.md): create the privacy policy from the template
- [`quote:check`](./quote-check.md): perform the calculation of each user storage quota and notify the user if over-quota
- `invite:purge`: purge expired account creation invites for all users
- [`appearance:downloadpicture`](./appearance-download-picture.md): Caches locally the picture defined in the appearance

For a complete list of available commands execute

```
php artisan
```

in the root folder of the source code.

For obtain help on the specific command you can execute the command with the `--help` option:

```
php artisan dms:test --help
```
