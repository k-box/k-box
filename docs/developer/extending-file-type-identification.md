# Extending File Type identification

The file type identification is managed by the File Service (`KBox\Documents\Services\FileService`). 
In particular by the `recognize($path)` method.

> A Facade to rapidly used the service is also available `KBox\Documents\Facades\Files`

## Identification process

The identification process starts with a file available on a storage disk.

1. The file is processed by the default type identifier
 - A mime type an document type are returned
2. The registered identifier that meet the execution criteria are instantiated and executed
 - The execution criteria is based on the `accept` clause of the identifier. 
   An identifier can run because it is registered to run everytime or based
   on the previously recognized mime type
3. Since point 2 can execute a list of identifiers the results needs to be flatten to a 
   single mime type and document type pair
 - if only one type identifier is executed, the output is used
 - if one executed identifier has a higher priority, its output is selected
 - if there are at least two identifier with the same priority, the most recurring mime 
   type and document type is selected
 - if the previous steps did not select a single <mimetype, documentType> the default
   identifier will be used


## Extending the type identification

The K-Box can be extended to support new mime types or to better identify both the document type
and mime type of an already identifiable mime type.

**Why an extension might be needed**

Let's consider a GeoJSON file. The default identifier might return `application/json` as the mime type and `CODE` 
as document type. This because a GeoJSON file is in fact a valid JSON file. A more specific type identifier 
could identify it with the appropriate <`application/geo+json`, `GEODATA`> mime type and document type.

**Adding a new type**

Adding a new mime type identification can be done via the `register` method on the `FileService` or `Files` facade.

It require to specify:

- the mimetype
- the [Document Type](../user/documents/type-identification.md#document-type) associated with it
- the file extension normally used for that file type
- the type identifier class that is able to identify the mime type from a file

```php
use KBox\Documents\Facades\Files;

Files::register($mimetype, $documenttype, $extension, MyTypeIdentifier::class);
```

The `MyTypeIdentifier` class can be defined like the following:

```php
class MyTypeIdentifier
{   
    /**
     * The accepted files.
     * 
     * Specify an array of mime types to get files matching 
     * that mime type or * to match all files
     * 
     * @var string|array
     */
    public $accept = "*";

    /**
     * The priority of this identifier.
     * Use positive integers, starting from 1, which is the lowest priority
     * 
     * @var int
     */
    public $priority = 1;

    /**
     * Identify a file mime and document type
     * 
     * @param string $path The path of the file to analyze
     * @param TypeIdentification $default The type identified by the default identifier
     * @return TypeIdentification the identified mime and document type
     */
    public function identify(string $path, TypeIdentification $default) : TypeIdentification 
    {
        // ...
    }
}
```

The `$accept` property specify `*` as a wildcard, so the type resolver is called everytime.
In alternative, if the mime type might be recognized from file wrognly identified with
other mime types, an array of mime types can be used as `$accept` property value.

```php
public $accept = ["application/json", "text/plain"];
```

> plugins can call `$this->registerType(...)` directly from the `Plugin` class, as it is an alias of `FileService::register(...)`
