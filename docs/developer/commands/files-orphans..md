# `files:orphans` command

Find and remove files not related to a Document Descriptor.

The following conditions should be met to consider a file as orphan:
- not a revision of another file
- not the first version of a File
- not in relation with a Document Descriptor

```
$ php artisan files:orphans [options]
```

**Options:**

- `--delete`: Put the orphan files in the trashed state.
- `--force`: Permanently delete the orphan files from the DMS.
- `--file-paths` : Output the paths on disk of the orphan files

If executed with no options, the output will list the orphan files ID and the state (i.e. if in trash), 
like in the code block below.

```
Searching for orphan files...
2 orphans found
example-document.pdf (file_id: 117)
example-presentation.pptx (file_id: 119) - (already trashed)
```
