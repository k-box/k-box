# Pages

The K-Box is not a Content Management System (CMS), although it requires some of the capabilities of a CMS.
Pages are one of these capabilities.

Pages let's you define static content that it is rendered when specific routes are invoked. One example can
be the privacy policy page.

> As of now the pages support is limited to the privacy policy and terms of service management

A page instance is represented via the `KBox\Pages\Page` class.

## Page identifiers and storage

To keep things extensible a page is required to have an identifier and a language attribute.
Both the page identifier and the language constitute the unique identifier of a page.

The page identifier is used to generally retrieve a page, while the language is used to explicitly retrieve a particularly localized version


### Storage format

Pages are stored as Markdown files with a YAML frontmatter

```markdown
---
id: page-id
title: The title of the page
description: A brief description of what this page is about
language: en
---

Page content

```

On disk the file name follows the pattern

```
{page-id}.{language}.md
```

Pages are stored in `/storage/app/pages`.

## Templates

The K-Box can offer page templates for specific pages. Each template is saved in `resources/assets/pages/stubs`
and follow the same storage format of a generic page.

Templates are used to help creating pages, as they cannot be modified or deleted.


## Configuring pages

...
