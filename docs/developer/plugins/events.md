## Events

Provides an observer mechanism to what happens inside the K-Box. 

### Subscribe to events from a Plugin

Adding an event listener to your plugin requires to create an event listener class. An event listener is a plain class with a `handle` method that receive the event object as parameter

```php
<?php

namespace MyPlugin\Listeners;

use KBox\Events\FileDeleted;

class SendDeleteNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  FileDeleted  $event
     * @return void
     */
    public function handle(FileDeleted $event)
    {
        // Access the file using $event->file...
    }
}
```

Once you have a listener, in your plugin `boot` method, call the `registerEventListener` method passing in the event class and the listener class.

```php

use KBox\Events\FileDeleted;
use MyPlugin\Listeners\SendDeleteNotification;

public function boot()
{
    $this->registerEventListener(FileDeleted::class, SendDeleteNotification::class);
}
```

### Available Events

- `KBox\Events\DocumentDescriptorDeleted`: a document descriptor is trashed or permanently deleted
- `KBox\Events\DocumentDescriptorRestored`: a document descriptor has been restores from the trash
- `KBox\Events\FileDeleted`: a file is trashed or permanently deleted
- `KBox\Events\FileDeleting`: a file will be trashed or permanently deleted. This fires before a file is deleted
- `KBox\Events\FileRestored`: a file has been restores from the trash