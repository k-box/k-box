# `quote:check` command

Perform the calculation of each user storage quota and notify the user if over-quota

## Usage

```
$ php artisan quote:check [-u|--user=USER]
```

The command, if no options are specified, will trigger an immediate used storage 
space calculation for each user. If the user is over threshold a notification
will be also queued.

Using the `--user` option, a list of users to limit the check to can be passed.

```bash
$ php artisan quote:check --user=1 --user=2
```
