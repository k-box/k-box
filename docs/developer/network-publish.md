# Publish on Network

- table `published_documents` is in 0..N relation with `document_descriptors`
  1 document can be published (on in processing of being published) on one or more network
- all fields are nullable, because something might not be implemented or not known at the time of the implementation
- document is published if marked as public (`is_public === true`) and has an entry in the `published_documents` table
- publish and unpublish operations are tracked
- published_at, published_by, unpublished_at, unpublished_by


### Publishing with video streaming service enabled

- Tracking `streaming_id` and `streaming_url`
- Adding video the first time and only if file update date is greather than publication published_at date
 - When updating the linked video stream, the old one is deleted and a new video stream is created
- Unpublishing causes the video stream to be deleted