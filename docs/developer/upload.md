# File upload and processing

## Upload

Files upload can happen in two ways:

1. Via form upload (subject or not on drag/drop)
2. Via the resumable upload protocol (subject or not on drag/drop)

From the user perspective the two approaches are mostly equal, but not on the backend implementation.
The form upload is synchrounous in the request, as the K-Box receives a request containing the file data, while the resumable upload is inheritly an asynchrounous process: the upload is started, but it could pause for a while before the K-Box can actually process the full file content.

To establishing a uniform document upload handling approach the _Asynchrounous Document Processing Pipeline_ is introduced.

The pipeline is based on the assumption that a `DocumentDescriptor` is always available and can assume different statuses, therefore a `DocumentDescriptor` can exists even if the file is not completly uploaded. 

The processing of the file is governed by events and the Laravel Queue.

In principles when an upload is finished a corresponding `UploadCompleted` event is triggered. This event is indepentent from the chosen upload mechanism, i.e. form or tus.

The `UploadCompleted` event listener will make sure the status of the `DocumentDescriptor` is `DocumentDescriptor::STATUS_UPLOAD_COMPLETED` and trigger the processing phase over the uploaded content, e.g. indexing it for search.

## Storage

Uploaded files are stored in a private space under `./storage/documents`.

Each upload is stored in a sub-folder structure based on year and month of upload and the file UUID. This is done mainly to prevent cases in which command iterating over the document list may hang the system, and to reduce the chance to reach the limits of files per folder.

The final path of a file will be 

```
./storage/documents/{year}/{month}/{file_uuid}/
```

e.g. `./storage/documents/2017/07/110ec58a-a0f2-4ac4-8393-c866d813b8d1`

> The filename is generated randomly and is not anymore connected to the original filename. This is done to reduce the chance that Unicode file names could not be stored on disk.

**File already existing**

For already existing files, the storage structure is not touched. The files are still located in `./storage/documents/{year}/{month}/` with the respective original filename


### File names

Files uploaded via form upload are saved in the storage with the original filename.

Files uploaded via TUS protocol are saved in the storage with the DocumentDescriptor UUID as filename. This is due to the fact that the TUS protocol don't take into consideration file names. The original filename is, therefore, only stored in the database as the result of the file upload authorization process.

## Upload Elaboration

See [Document Elaboration](./document-elaboration.md)
