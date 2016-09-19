# Supported File Types

Here is the comprehensive list of supported mime types and their associated file extension.

In the table with *Is Searchable* we mean that can be added to the K-Link Core and the Core can extract text from the document and perform search over that text. With *Is Previewable* we mean that the DMS can show a preview of the content of the file (this is not the thumbnail).

Possible values for *Is Searchable*:

- basic: Search will include file name and title (plus some metadata depending on the file type)
- normal: Search will include the content of the file
- advanced: Advanced features are supported

Possible values for *Is Previewable*:

- yes: You will get a preview of the file content inside the DMS
- partial: You will get a link to the file preview
- no: You are not able to get a preview of the file's content 


| Mime type                                                                 | Extension    | Is Searchable | Is Previewable | Remarks                                                                                |
| ------------------------------------------------------------------------- | ------------ | ------------- | -------------- | -------------------------------------------------------------------------------------- |
| text/html                                                                 | html, htm    | normal        | yes            |                                                                                        |
| application/msword                                                        | doc          | normal        | no             | Office 2003 Word Document                                                              |
| application/vnd.ms-excel                                                  | xls          | normal        | no             | Office 2003 Excel Spreadsheet                                                          |
| application/vnd.ms-powerpoint                                             | ppt          | normal        | no             | Office 2003 Powerpoint Presentation                                                    |
| application/vnd.openxmlformats-officedocument.spreadsheetml.sheet         | xlsx         | normal        | no             | Office 2007-2016 Excel Spreadsheet                                                     |
| application/vnd.openxmlformats-officedocument.presentationml.presentation | pptx         | normal        | no             | Office 2007-2016 Powerpoint Presentation                                               |
| application/vnd.openxmlformats-officedocument.wordprocessingml.document   | docx         | normal        | yes            | Office 2007-2016 Word Document                                                         |
| application/pdf                                                           | pdf          | normal        | yes            |                                                                                        |
| text/uri-list                                                             | uri          | no            | no             | List of URIs according to the [RFC 2483](http://tools.ietf.org/html/rfc2483#section-5) |
| image/jpg                                                                 | jpg          | basic         | yes            |                                                                                        |
| image/jpeg                                                                | jpeg         | basic         | yes            |                                                                                        |
| image/gif                                                                 | gif          | basic         | yes            |                                                                                        |
| image/png                                                                 | png          | basic         | yes            |                                                                                        |
| image/tiff                                                                | tiff         | basic         | yes            |                                                                                        |
| text/plain                                                                | txt          | normal        | yes            | Plain text (ASCII or UTF-8 encoded)                                                    |
| application/rtf                                                           | rtf          | normal        | no             | Rich Text Format                                                                       |
| text/x-markdown                                                           | md, markdown | normal        | yes            | Markdown format                                                                        |
| application/vnd.google-earth.kmz, application/vnd.google-earth.kml+xml    | kml, kmz     | normal        | no             | Google Earth file (aka Keyhole Markup Language)                                        |
| application/vnd.google-apps.document                                      | gdoc         | basic         | partial        | Google Document                                                                        |
| application/vnd.google-apps.presentation                                  | gslides      | basic         | partial        | Google Slides                                                                          |
| application/vnd.google-apps.spreadsheet                                   | gsheet       | basic         | partial        | Google Spreadsheet                                                                     |

Other file types not directly mentioned in the previous table can be uploaded to the DMS and only the filename will be used for the search functionality.
