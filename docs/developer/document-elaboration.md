# Document Elaboration

Once uploaded a document can be further processed. To this aim the Document Elaboration pipeline is established.

The pipeline include actions that can modify the document metadata or use the document for 
other aims, e.g. generating a thumbnail.

The [`elaboration.php` configuration](../../config/elaboration.php) file define the set of actions, executed 
sequentially, that will be applied to the newly created `DocumentDescriptor`. For example:

- Thumbnail generation
- Add to search index
- Further metadata extraction from the file

All actions in the pipeline are executed sequentially, one after the other, in the same queued Job. 
Some actions might just dispatch other jobs.

## Error handling

Action might fail. Throwing an exceptions is a case of failure, while returning null is not considered a failure.

Throwing an exception can cause the entire pipeline to stop. If the action can fail and its computation discarded, 
then define the `$canFail` property on the action class and set it to `true`:

```php
protected $canFail = true;
```

In this case throwing an exception inside the `run` method will make the pipeline continue to the next step. The error is automatically logged.


## Extension

The document elaboration pipeline can be extended in two ways:

1. change the pipeline configuration in the `elaboration.php` configuration file
2. add actions at runtime from a service provider


### Create an elaboration action

Every action must extends the `KBox\Contracts\Action` class. The only required method is `run`. 
It receives the `DocumentDescriptor` to process. The `run` method must return the 
`DocumentDescriptor` instance that is the result of the elaboration. 
An action might extract the title from the file, or guess the language of the file content.

```php
namespace MyPackage;

use KBox\Contracts\Action;

class MyAction extends Action
{
    /**
     * Execute the action over the DocumentDescriptor
     * 
     * @param \KBox\DocumentDescriptor $descriptor
     * @return \KBox\DocumentDescriptor
     */
    public function run($descriptor)
    {
        // Here you perform the task and return 
        // the updated $descriptor to the 
        // next action in the pipeline
        return $descriptor;
    }
}
```

### Add, at runtime, a new action to the pipeline

The elaboration pipeline can be extended at runtime, by adding other actions.

You can do so by calling the `register` method on the `DocumentElaboration` facade 
and specify the action class to add. To be effective do the registration 
in a service provider.

```php

use MyPackage\MyAction;
use KBox\DocumentsElaboration\Facades\DocumentElaboration;

class PackageServiceProvider
{
    public function boot()
    {
        DocumentElaboration::register(MyAction::class);
    }
}
```
