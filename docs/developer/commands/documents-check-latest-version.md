# `documents:check-latest-version` command

Check if the document mime type, type and hash are updated to the latest file version.

**is highly recommended to put the K-Box in maintenance mode and have a backup of the database before executing the command**


## Usage

```
$ php artisan documents:check-latest-version [document]
```

The `document` parameter is optional, if specified must be the `id` of a Document Descriptor. 
If not specified all currently saved document descriptors are taken into consideration.

An example output is shown in the code block below

```
Checking and fixing 70 documents...
The following documents must be reindexed: 28 54 56 81
```

In this case 70 document descriptors were checked and only the one with ID `28 54 56 81` 
were changed to report the latest file version information on the Descriptor. 

In case no descriptors contains problems the output will be similar to the following

```
Checking and fixing 70 documents...
No documents with problems found.
```


## What changes are applied

The command will operate on the following `DocumentDescriptor` fields:

- `document_type`
- `mime_type`
- `hash`
