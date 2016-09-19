# Maintenance mode

The DMS has a maintenance mode were HTTP requests are not processed.

## Enable

To enable the maintenance mode the command 

```
$ php artisan down
```

must be executed. This command is only available from command line, and therefore the physical access (e.g. ssh) to the DMS instance is required.

## Disable

To disable the maintenance mode the command 

```
$ php artisan up
```

must be executed. This command is only available from command line, and therefore the physical access (e.g. ssh) to the DMS instance is required.