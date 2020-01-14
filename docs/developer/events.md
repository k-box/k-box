# Events

The K-Box has an internal event system based on [Laravel's Events](https://laravel.com/docs/5.7/events).
Events are used to track actions and workflows. An event can also trigger workflows and be used to process 
asynchronous notifications or long-running actions.


## Reference

| Event                        | Description |
| ---------------------------- | ----------- |
| `DocumentDescriptorDeleted`  | A document descriptor was trashed by a user |
| `DocumentDescriptorRestored` | A document descriptor was restored from the trash |
| `EmailChanged`               | The user changed the email address |
| `FileDeleted`                | A file is trashed |
| `FileDeleting`               | It is fired immediately before a file is trashed or permanently deleted. |
| `FileDuplicateFoundEvent`    | A duplicate of an existing file has been recognized |
| `FileRestored`               | A file is restored from the trash |
| `PageChanged`                | A static page, like terms of service, privacy policy has been changed on disk |
| `PersonalExportCreated`      | The export of the user's personal data has been created |
| `PrivacyPolicyUpdated`       | The privacy policy file was changed |
| `ProjectCreated`             | A project has been created by a user |
| `ShareCreated`               | A share of a file or collection was created |
| `UploadCompleted`            | A file upload completed |
| `UserInviteAccepted`         | An invitation was accepted and the account created |
| `UserInvited`                | An invitation request is sent to a potential user |
