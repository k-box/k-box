# Changelog

0.12.4 bugfix release.

## Changes

- bugfix: error while running the reindex command from Administration > Storage
- bugfix: Drag and drop a folder in a project now creates collections under a project
- enhancement: Drag and drop over a collection on the sidebar starts the upload 
  in that collection. No more strange message is shown
- enhancement: better error message when upload size exceed the configured limit
- enhancement: default file limit for upload is now exactly 200MB (was 198MB previously)

## Upgrade barometer

No migration or database changes, so no lengthy downtime is expected