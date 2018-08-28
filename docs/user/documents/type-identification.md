# Type identification

## Mime Type



## Document Type

Each document is assigned a document type. The document type is a generic categorization of the documents created for filtering and identification purposes.

This is usually a word describing in a generic way what the document is about, e.g. image, video...

The document type is based on the file [mime type](https://tools.ietf.org/html/rfc2046) with additional logic in case multiple documents have the same mime type. For example a tiff file can be an image or contain geographic data to be plotted on a map.

**list of supported document types**

- See KBox\Documents\DocumentType






##### Developer, adding new document type

**mime type based**

If the document type can be selected by looking at the mime type, add a new document type mapping mean adding an entry in the associative array KBox\Documents\DocumentType::$mimeTypesToDocType


```php
public static $mimeTypesToDocType = [
    // ...
    
    'new/mime-type' => self::DOCUMENT,

];
```
