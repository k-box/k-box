# Document Descriptor and File statuses

Considering the asynchronous nature of some actions, both `File` and `DocumentDescriptor` can have different status according to the uploading and processing pipeline.

## File

- `UPLOADING`: The file is being uploaded to the system. Mostly when a chunk based upload is in progress
- `COMPLETED`: The file upload was completed and all the file content is in the system storage
- `CANCELLED`: The file upload was cancelled by the user


## DocumentDescriptor

The status of the DocumentDescriptor inherits also the status of the File it describes:

- `UPLOADING`: The referenced file is being uploaded to the system. Mostly when a chunk based upload is in progress
- `UPLOAD_CANCELLED`: The referenced file upload was cancelled by the user
- `UPLOAD_COMPLETED`: The file upload was completed


In addition to inherited statuses, the following are referring only to the document descriptor:

- `PROCESSING`: The file is being post-processed (e.g. metadata extraction, indexing,...)
- `COMPLETED`: The file upload and the processing is complete
- `ERROR`: The processing phase raised an error
