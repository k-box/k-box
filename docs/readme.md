# K-Box Documentation

This folder contains the K-Box documentation.

The documentation targets users, IT person, and developers.

- [User](./user/index.md)
- [Release Notes](./release-note/index.md)
 - [Latest version (0.24)](./release-note/release-note-0.24.md)
- [Developer](./developer/index.md)

## Structure

The `release-note` folder contains the presentation of the features included in a release.
Release notes are created only for Major and Minor releases, patch releases are only documented as part of the [changelog](../changelog.md) file.

The `administration` folder contains documentation related to K-Box instance management.

The `installation` folder contains documentation on how to install a K-Box on your server.

The `user` folder contains documentation related to the usage of the K-Box from the user perspective.

The `developer` folder contains documentation strictly related to the code and how thing works.
Lot of developer documentation is also directly contained in the source code files as comments.

_example structure_

```
|-- docs
    |-- administration
    |-- installation
    |-- user
    |-- developer
    |-- release-note
```

## How to contribute

Of course, we accept contribution. Documentation is a key aspect to empower new developers and new users.

The documentation consists in [Markdown](https://daringfireball.net/projects/markdown/) files with a YAML frontmatter.

**Language rules**

The documentation must be written in English.

**Format**

The documentation must be written in [Markdown](https://daringfireball.net/projects/markdown/) files (with `.md` extension). Each file must have a lower case filename with no spaces. To separate words use a dash `-`.

At the beginning of each file a YAML frontmatter needs to be inserted. The frontmatter serve as metadata container for presentation purposes.

An example frontmatter is in the following code block

```yaml
---
Slug: 0.20.0
Title: K-Box v0.20 (February 2017)
Description: What's new in the K-Box
Order: 0
---
```

**File naming rules**

When naming files please use

- `.md` extension,
- no spaces,
- use `-` as word separator,
- entirely lower case

**Folders**

User or developer documentation can be organized in sub-folders. We encourage to not use more than level of folders to keep the organization clean.

When naming folder please use

- lower case,
- no spaces,
- `-` for word separation

**Images**

Images can be used within the documentation. Image files must reside in a folder called `images` as sub-folder of the one that contains the file you want to insert the image into.
