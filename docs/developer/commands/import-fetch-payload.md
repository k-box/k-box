# import:fetch-payload command

This command enables to gather the original job payload that has been pushed to the Jobs queue for handling an import.

The command checks only failed jobs and can get payload only of import with the _error_ state. 

```
> php artisan import:fetch-payload [--replace] {import}
```

_Parameters:_

- `{import}`: The identifier of the Import you want to search the job payload

_Options:_

- `--replace`: force the replace of the existing job payload (attached to the import) if a new one is found

If multiple failed jobs mention the selected `{import}` the command will show the identifier of the jobs and the failure timestamp to let you specify which job source to use.
