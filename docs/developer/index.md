# K-Link DMS 
# Developer documentation


## Introduction

...


## Command Line

The DMS exposes a set of commands through a command line interface in addition to the ones available from the framework used for developing the DMS itself.

The command line tool set is exposed using [Laravel's Artisan](https://laravel.com/docs/5.1/artisan) command line. To see all the available commands use

```
> php artisan
```

The DMS specific commands are:

- `dms:update`: Performs first installation and updates
- `dms:reindex`: Perform the reindexing of the currently indexed documents
- `dms:sessions`: Get the user's session status
- `dms:test`: This command will test the K-Link Core configuration and connection
- [`dms:update`](commands/update-command.md): Perform the installation/update steps for the K-Link DMS
- `dms:queuelisten`: Start listening for jobs on the queue and report the status to the admin interface
- `dms:sync`: Performs a synchronization of the documents that do not exists in the Core, but can be found in the DMS
- [`dms:import`](commands/import-command.md): Import collections, projects and documents from a folder on a the filesystem
- [`users:import`](commands/user-import-command.md): Import users from a CSV file
- `dms:lang-publish`: Publish language files for the frontend
- `collections:clean-duplicates`: Clean the duplicated documents contained in a collection
- `collections:list`: List collections as viewed from a user
- [`import:fetch-payload`](commands/import-fetch-payload.md): Take the failed job payload and associate it to the given import
- `thumbnail:generate`: Generate the thumbnail of a Document
- [`documents:check-affiliation`](commands/documents-check-affiliation.md): Check if there are documents assigned to a different institution than the user uploader one



## Support and Maintenance

Sometimes a support request is about a user that is experiencing problems during the normal operations on the DMS. 

From the perspective of the support different actions can be made. Here is the list of the operations that can be performed:

- [View the current log file](./support/view-logs.md)
- [Clear the DMS cache](./support/clearing-cache.md) (_requires physical access_)
- [Transfer Project ownership to another Project Administrator](./support/transfer-project-ownership.md)  (_requires physical access_)


### First step of a techinical support request solving

The very first step of solving each support request is gather the largest amount of information from the user about the action he/she has performed. This includes: 

- visited pages
- the context information reported in the ticket request
- user ID
- collections identifiers (if needed)
- document identifiers (if needed)
- log entries that are around the time of the problem

Sometimes log entries reveal the nature of the error showed to the user, sometimes not. In the latter case assign the ticket to a developer.


## Testing

### Automatic tests

After each code push to git.klink.asia the system will automatically execute a syntax check over every PHP file.

### Manual triggered tests

- [Unit Tests](testing/unit-tests.md)
- [Test Instance](testing/test-instance.md)
