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

Some default thumbnail generator requires [ImageMagick](https://www.imagemagick.org/script/index.php).
You can install it as a [PECL extension](https://pecl.php.net/package/imagick).

For Windows installation you might want to follow [this guide](https://mlocati.github.io/articles/php-windows-imagick.html)

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
