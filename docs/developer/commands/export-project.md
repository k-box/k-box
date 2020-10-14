# `export:project` command

Export a project content as folders and files in a ZIP archive.

## Usage

```
php artisan export:project {id}
```

The `{id}` argument is mandatory and must be the identifier of the project. You can find it in the URL 
when editing the project.

A zip file will be generated with the content of the project plus some additional files

- `documents.csv` lists the documents contained in the export with the basic metadata (title, folder, author, language, ...).
The file is formatted according to the Comma Separated Value standard using 8-character UTF encoding.
- `project-abstract.txt` contains the description of the project, if added.
- `readme.txt` contains some useful information on the content of the zip and how to deal with it.
- `a folder`, named as the project title, containing the project files and subfolders.


### documents.csv

The documents.csv file has the following columns

- `id`: The unique identifier of the document
- `title`: The title of the document
- `uploaded_at`: When the document was added to the K-Box
- `file`: The location of the file inside the zip archive
- `language`: The recognized language of the document
- `document_type`: The format of the document, e.g. pdf-document, image, ...
- `uploader`: The user who uploaded the document
- `authors`: The document's author(s), if added
- `license`: The license of the document
- `projects`: The project that contained the document
- `collections`: The collection where the document was added
- `hash`: An alphanumeric string that can be used to verify that the content of the document has not been altered
- `url`: The url of the document inside the K-Box

The file may contain duplicates in the `id` column, as the same document can be added to multiple collections.
Each document is represented according to the folders that are added.
