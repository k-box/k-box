# `dms:reindex` command

Perform the reindexing of the currently indexed documents.

**this command must be executed in maintenance mode** and is highly recommended to have a backup of the system.

```
$ php artisan dms:reindex [options] [--] [<documents>]
```


**Arguments:**

- `documents`: The list of documents to reindex given in the form of IDs

**Options:**

- `--only-public`: Consider only the documents that have been published on the network and update only the public version.
- `-f|--force`: Force the rebuild of the document and thumbnail URL.
- `--klink-id`: Interpret the `documents` argument as local document id instead of database id
- `--skip=SKIP`: Enable to skip some documents from the batch operation. Zero based value. Normally used in conjunction with limit
- `--take=TAKE`: The maximum number of documents per batch. If no limit is specified all documents will be reindex in a single batch
- `-u`, `--users=USERS`: Filter for documents of a specific user. The ID of the User is here expected (multiple values allowed)
- `--env[=ENV]`: The environment the command should run under.
  
