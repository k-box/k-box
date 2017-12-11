# Import files, collections and projects with collections and documents

Starting from K-Box version 0.7.0 the import from folder has been enhanced. Now can

- import from the same K-Box storage folder (or any folder without copying the files)
- import folders without a parent as Projects
- better File Already Exists conflicts resolution

The ability to perform the mass document and collection import is available only in the form of a maintenance command, therefore physical access to the running K-Box instance is required. Also an existing administration or Project Manager account is required because this procedure requires a login.

## The Command

```shell
php artisan dms:import [--local|-l] [--create-projects|-p] [--user|-u] [--also-current-folder|-c] [--skip] [--exclude] [--attempt-to-resolve-file-conflict|-d] {folder}
```

**When run the login with an administrator or a Project Manager account will be asked.**

**Imported documents, collections and projects will be assigned to the user that made the login, unless the `--user` parameter is specified**

### Parameters

- `{folder}`: the path of the folder you want to import.

### Options

- `--local' or '-l': Consider the folder as the storage path and do not copy files, but only performs indexing and collection creations.
- `--create-projects' or '-p': Create projects from folders without a parent
- `--user' or '-u': Specify the user that will be the owner of the created collections and documents
- `--also-current-folder' or '-c': Use the specified folder argument as the source for all collections and import files that are stored in it
- `--skip': Skip the folders that match the specified pattern
- `--exclude': Exclude files that match the specified pattern
- `--attempt-to-resolve-file-conflict' or '-d': when a duplicated file is found attempts to add the original file also the to the other collection

## Running

Let's consider an example execution of the command

```shell
php artisan dms:import --local --create-projects .\tests\data\folder_for_import
```

Which will tell to create projects from sub-folder that can be found in `.\tests\data\folder_for_import` and also to not copy the files from the folders to the K-Box storage folder (`--local` option)

Consider that the folder `.\tests\data\folder_for_import` has the following structure

```
|-- folder1
|   |-- subfolder1
|   |   |-- in-sub-folder-1.md
|   |-- in-folder-1.md
|-- folder2
|   |-- subfolder2
|   |   |-- in-sub-folder-2.md
|   |-- subfolder3
|   |   |-- in-sub-folder-3.md ## has the same content as "in-sub-folder-2.md"
|   |-- in-folder-2.md
```

The command output will be similar to the transcript below

```
K-Box Import command. Please login before proceeding
Gathering folder structure for .\tests\data\folder_for_import
Enqueuing 1114:folder1 as import 568 in group 4051:folder1
Preparing import from .\tests\data\folder_for_import\folder1
  1 files found
  Importing .\data\folder_for_import\folder1\in-folder-1.md
  done.
Enqueuing 1116:subfolder1 as import 569 in group 4052:subfolder1
Preparing import from .\tests\data\folder_for_import\folder1\subfolder1
  1 files found
  Importing .\tests\data\folder_for_import\folder1\subfolder1\in-subfolder-1.md
  done.
Enqueuing 1118:folder2 as import 570 in group 4053:folder2
Preparing import from .\tests\data\folder_for_import\folder2
  1 files found
  Importing .\tests\data\folder_for_import\folder2\in-folder-2.md
  done.
Enqueuing 1120:subfolder2 as import 571 in group 4054:subfolder2
Preparing import from .\tests\data\folder_for_import\folder2\subfolder2
  1 files found
  Importing .\tests\data\folder_for_import\folder2\subfolder2\in-subfolder-2.md
  done.
Enqueuing 1122:subfolder3 as import 572 in group 4055:subfolder3
Preparing import from .\tests\data\folder_for_import\folder2\subfolder3
  1 files found
  Importing .\tests\data\folder_for_import\folder2\subfolder3\in-subfolder-3.md
  > File already exists.
  > Found 1121:in-subfolder-2.md at .\tests\data\folder_for_import\folder2\subfolder2\in-subfolder-2.md
  >>> JOB FAILED: import 572 Processed 0/1
Import process completed.
```

For each sub-folder the number of files that can be imported will be showed and you will see a line, that starts with `Importing`, for each file that will be processed. For each folder the stored File instance id and the Import id are listed.

## File Existence conflict resolution

When the `dms:import` is run with the option `--attempt-to-resolve-file-conflicts` the system will try to identify the document with the same fingerprint and attempt to add that document to the current collection that was created from the folder.

The document fingerprint is calculated using the SHA-256 digest on the file content. The documents can have different titles, but if the content is identical they will be considered as one document.

When a document is merged the abstract will be populate with a phrase that report the file name of the duplicated file.

An example output of the import command when run with the option `--attempt-to-resolve-file-conflicts` is reported below

```
K-Box Import command. Please login before proceeding
Gathering folder structure for .\tests\data\folder_for_import
Enqueuing 1114:folder1 as import 568 in group 4051:folder1
Preparing import from .\tests\data\folder_for_import\folder1
  1 files found
  Importing .\data\folder_for_import\folder1\in-folder-1.md
  done.
Enqueuing 1116:subfolder1 as import 569 in group 4052:subfolder1
Preparing import from .\tests\data\folder_for_import\folder1\subfolder1
  1 files found
  Importing .\tests\data\folder_for_import\folder1\subfolder1\in-subfolder-1.md
  done.
Enqueuing 1118:folder2 as import 570 in group 4053:folder2
Preparing import from .\tests\data\folder_for_import\folder2
  1 files found
  Importing .\tests\data\folder_for_import\folder2\in-folder-2.md
  done.
Enqueuing 1120:subfolder2 as import 571 in group 4054:subfolder2
Preparing import from .\tests\data\folder_for_import\folder2\subfolder2
  1 files found
  Importing .\tests\data\folder_for_import\folder2\subfolder2\in-subfolder-2.md
  done.
Enqueuing 1122:subfolder3 as import 572 in group 4055:subfolder3
Preparing import from .\tests\data\folder_for_import\folder2\subfolder3
  1 files found
  Importing .\tests\data\folder_for_import\folder2\subfolder3\in-subfolder-3.md
  > File already exists.
  > Found 1311:in-subfolder-2.md at .\tests\data\folder_for_import\folder2\subfolder2\in-subfolder-2.md
  > Attempting to merge document descriptors...
  >   done.
Import process completed.
```

As can be seen in the output

```
  Importing .\tests\data\folder_for_import\folder2\subfolder3\in-subfolder-3.md
  > File already exists.
  > Found 1311:in-subfolder-2.md at .\tests\data\folder_for_import\folder2\subfolder2\in-subfolder-2.md
  > Attempting to merge document descriptors...
  >   done.
```

The file `in-subfolder-3.md` has the same content as `in-subfolder-2.md`, which has the id `1311` in the K-Box. Then the Document Descriptor has been searched and on that (the one that is attached to file id `1311`) the group `subfolder3` is added.