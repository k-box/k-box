---
Title: Document Types
Description: user documentation
---

# Type detection

Files added to the K-Box are subject to detection. With detection we refer 
to the process of getting the [_mime type_](https://www.iana.org/assignments/media-types/media-types.xhtml) 
and the _document type_ of the file. The pair _mime type_ and _document type_ is used to identify if 
the various processing actions supports the uploaded file. For example if a thumbnail or a preview can 
be generated.

## Mime Type

The [mime type](https://tools.ietf.org/html/rfc2046) is defined by standard bodies. The detection is 
performed by a library that uses the [file magic number](https://en.wikipedia.org/wiki/File_format#Magic_number) 
and a known list of extensions to mime type. The normalization with the list is performed to reduce differences 
between the file library between Operating Systems.

## Document Type

Each file is assigned a document type. The document type is a generic categorization of the documents created 
for filtering and description purposes.

This is usually a word describing in a generic way what the document is about, e.g. image, video...

The document type is based on the file [mime type](https://tools.ietf.org/html/rfc2046) with additional logic 
in case multiple documents have the same mime type. For example a tiff file can be an image or contain 
geographic data to be plotted on a map.

**Defined document types**

| Type            | Description                                                                     |
|-----------------|---------------------------------------------------------------------------------|
| `ARCHIVE`       | A compressed file, for example a zip or tar file                                |
| `AUDIO`         | Audio file                                                                      |
| `EMAIL`         | A saved email message                                                           |
| `BINARY`        | A generic binary file, can be an executable or an unknown file in binary format |
| `CALENDAR`      | The file is a calendar entry, maybe ICS                                         |
| `CODE`          | A file that contains code, like a cpp source file                               |
| `DOCUMENT`      | A generic document. Sometimes used also for Word and PDF                        |
| `DVD_VIDEO`     | A Video that is a DVD file                                                      |
| `FORM`          | A series of questions or fields                                                 |
| `GEODATA`       | Geographic data. A file that contains geographical referenced data              |
| `IMAGE`         | Image file                                                                      |
| `NOTE`          | The file comes from a note taking application, like OneNote                     |
| `PDF_DOCUMENT`  | A PDF document                                                                  |
| `PRESENTATION`  | A presentation, made for example with Power Point                               |
| `SPREADSHEET`   | A spreadsheet                                                                   |
| `TEXT_DOCUMENT` | A generic plain text file                                                       |
| `URI_LIST`      | A file that contains a list of URLs/URIs                                        |
| `VIDEO`         | Video file                                                                      |
| `WEB_PAGE`      | An html file                                                                    |
| `WORD_DOCUMENT` | A word processing document, created for example with Microsoft(tm) Word(tm)     |

> The list of document types is defined as constants in the class `KBox\Documents\DocumentType`

## Adding new type or extending the type identification system

See [Extending file type identification](../developer/extending-file-type-detection.md) in the developer section.
