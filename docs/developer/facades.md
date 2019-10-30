# K-Box facades 

Facades provides proxied access to K-Box services.

## Facade reference

Below you will find the list of available facades, their location underlying classes. 
This is a useful tool for quickly digging into the API documentation.

| Facade name         | namespace                           | Class |
|---------------------|-------------------------------------|
| Files               | `KBox\Documents\Facades`            | [`KBox\Documents\Services\FileService`](../../packages/contentprocessing/src/Services/FileService.php) |
| Thumbnails          | `KBox\Documents\Facades`            | [`KBox\Documents\Services\ThumbnailsService`](../../packages/contentprocessing/src/Services/ThumbnailsService.php) |
| Previews          | `KBox\Documents\Facades`            | [`KBox\Documents\Services\PreviewService`](../../packages/contentprocessing/src/Services/PreviewService.php) |
| DocumentElaboration | `KBox\DocumentsElaboration\Facades` | [`KBox\DocumentsElaboration\DocumentElaborationManager`](../../app/DocumentsElaboration/DocumentElaborationManager.php) |
| KlinkStreaming      | `KBox\Facades`                      | [`Oneofftech\KlinkStreaming\Client`](https://github.com/OneOffTech/k-link-streaming-upload-client/blob/master/src/Client.php) |


## Laravel Facades

As the K-Box is based on Laravel, we suggest to read also the [Laravel Facade documentation](https://laravel.com/docs/5.7/facades).
