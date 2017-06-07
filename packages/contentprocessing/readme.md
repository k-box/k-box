# Preview and Thumbnail generation services

This package contains the

- PreviewService
- ThumbnailsService


## Add a new Preview renderer to support new file formats

create the class that respect the Content\Contracts\Preview interface in the folder Preview, 
then add it in the `PreviewFactory` to the `LOADER_MAPPING` constant.

