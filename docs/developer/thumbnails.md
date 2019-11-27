# Thumbnails

Thumbnail generation is performed by the `ThumbnailsService` with the help of thumbnail generator classes.

For easier access the service is also available via the `KBox\Documents\Facades\Thumbnails` facade.

A thumbnail generator receives a `KBox\File` instance and return a `ThumbnailImage` instance.
This is done to abstract from the storage layer and give a uniform api for manipulating images.

The `ThumbnailImage` uses the [Intervention Image](http://image.intervention.io/getting_started/introduction) image


### How a thumbnail is generated

- The thumbnail generation process starts from the `Thumbnails::generate(File $file)` call
- A registered generator is selected by checking the mime type and invoking the `isSupported(File)` 
  method of the generators for the given mime type
- The first available generator is used

### Write a Thumbnail Generator

A thumbnail generator can be created by implementing the `KBox\Documents\Contracts\ThumbnailGenerator` interface.

The interface defines three methods:

- `generate` generate a thumbnail image
- `isSupported` verify if a given file is supported by the generator
- `supportedMimeTypes` return the supported mime types

```php
use KBox\File;
use KBox\Documents\DocumentType;
use KBox\Documents\Thumbnail\ThumbnailImage;
use KBox\Documents\Contracts\ThumbnailGenerator;

class PngThumbnailGenerator implements ThumbnailGenerator
{
    public function generate(File $file) : ThumbnailImage
    {
        return ThumbnailImage::load($file->absolute_path)->widen(ThumbnailImage::DEFAULT_WIDTH);
    }

    public function isSupported(File $file)
    {
        return in_array($file->mime_type, $this->supportedMimeTypes()) && $file->document_type === DocumentType::IMAGE;
    }

    public function supportedMimeTypes()
    {
        return [
            'image/png',
        ];
    }
}
```

The custom generator can be registered using the facade

```php
use KBox\Documents\Facades\Thumbnails;

Thumbnails::register(PngThumbnailGenerator::class);
```

### Requirements

Thumbnail generation requires `gd` or `imagick` extension installed and active. 
If ImageMagick extension is found it will be used as default library for manipulating images.

Specific thumbnail generators can have additional requirements, the table below is a non-exhaustive 
list of requirements for default thumbnail generators:

| image format                | additional requirements | generator class |
|-----------------------------|-------------------------|-----------------|
| jpg, gif, png               | -                       | `KBox\Documents\Thumbnail\ImageThumbnailGenerator` |
| pdf                         | `imagick` extension, [ImageMagick and Ghostscript](#obtain-imagemagick-and-ghostscript) | `KBox\Documents\Thumbnail\PdfThumbnailGenerator` |
| mp4                         | [FFmpeg](./developer-installation.md#dependencies-installation) | `KBox\Documents\Thumbnail\VideoThumbnailGenerator` |
| geojson, geotiff, shapefile | Geo Plugin and an active GeoServer instance | `KBox\Geo\Thumbnails\*` |


#### Obtain ImageMagick and Ghostscript

The installation of ImageMagick and Ghostscript might be different between operating systems. This section do not want to cover the whole installation process, but give pointers to what we tested.

**Linux**

Depending on your distribution there might be packages to install the required dependencies.
Assuming that you have PHP installed on your Operating System, the next code block shows how we install Imagemagick and Ghostscript on our 
Continuous Integration and within the Docker image:

```bash
apt-get install libmagickwand-dev
apt-get install ghostscript
pecl channel-update pecl.php.net
pecl install imagick-3.4.4
```

> We tested manually the following versions: 
> - Imagick Pecl 3.4.4 compiled with ImageMagick 6.9.10-23 Q16 x86_64 with Ghostscript 9.27 on Debian 9 and 10.1
> - Imagick Pecl 3.4.4 compiled with ImageMagick 6.8.9-9 Q16 x86_64 with Ghostscript 9.26 on Ubuntu 16.04 (via Travis CI)

**Windows**

You need to download the [imagick Pecl extension](https://pecl.php.net/package/imagick) and [Ghostscript](https://github.com/ArtifexSoftware/ghostpdl-downloads/releases).

> We tested manually the following versions: Imagick Pecl 3.4.4 compiled with ImageMagick 7.0.7-11 Q16 x64 with Ghostscript 9.25 on Windows 10

For a more in-depth installation procedure please refer to [this guide](https://mlocati.github.io/articles/php-windows-imagick.html)


### Testing

A fake ThumbnailsService can be created using the facade. The fake instance replace the 
default instance in the application container and offer some assertion methods.

```php
use KBox\Documents\Facades\Thumbnails;

Thumbnails::fake();
```

| Assertion                          | Description                                                               |
|------------------------------------|---------------------------------------------------------------------------|
| `assertGenerateCalled(File $file)` | Assert if the `generate` method is called with the given `File` instance  |
| `assertQueued(File $file)`         | Assert that a thumbnail generation job is queued for the given `File`     |
| `assertNotQueued(File $file)`      | Assert that a thumbnail generation job is not queued for the given `File` |
| `assertNothingQueued()`            | Assert that no thumbnail generation job is queued                         |
