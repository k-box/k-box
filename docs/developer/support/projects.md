# Projects related errors

## Error renaming a project

If the following error message appears 

```
The project cannot be created. (Argument 2 passed to KBox\Documents\DocumentServices::updateGroup() must be an instance of KBox\Group, null given, called in /var/www/dms/app/Http/Controllers/Projects/ProjectsController.php on line 226 and defined)
```

when renaming a project it means that the project root collection has been trashed or 
permanently deleted. From the UI perspective trashing/deleting a project root collection 
is not permitted so the action implies a human interaction on the K-Box instance using the command line tools.

To resolve the issue restore the trashed collection, referenced by the `collection_id` on the Project entity.