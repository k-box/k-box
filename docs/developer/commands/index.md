# K-Box Command Line Tools

> The usage of the command line options requires direct access to the running 
> K-Box instance.


The K-Box command line suite of commands rely on the [Laravel Artisan CLI](https://laravel.com/docs/5.2/artisan).

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
- [`dms:import`](./import-command.md): Import collections, projects and documents 
  from a folder on a the filesystem
- `dms:lang-publish`: Publish Javascript language files for RequireJS i18n plugin
- [`users:import`](./user-import-command.md): Import users from a CSV file
- [`import:fetch-payload`](./import-fetch-payload.md): Take the failed job payload and 
  associate it to the given import
- [`documents:check-affiliation`](./documents-check-affiliation.md): Check if all the 
  documents has the same institution of the first uploader. The command assumes that 
  the user affiliation has not been changed since the upload of the document
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

For a complete list of available commands execute

```
php artisan
```

in the root folder of the source code.

For obtain help on the specific command you can execute the command with the `--help` option:

```
php artisan dms:test --help
```
