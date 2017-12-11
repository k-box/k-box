# Import Jobs

In this section we refer to Import Jobs as the one started from `Documents > Create or Add > Import` section or the ones triggered from the `dms:import` command.

Imports are ususally from URL or folders (local or accessible at the filesystem level).


## Import cannot be retried due to job payload not available

Import that failed before the upgrade to the K-Box version 0.8.0 do not have the job payload information. Jobs are the actual action that is executed asynchronously.
Each job contains a payload with the information of what needs to be executed.

Imports are handled by Jobs, therefore to retry one of them in the same exact configuration the original job payload needs to be attached to the import.

With the command `import:fetch-payload` the import job can be retrieven and associated to the Import in _error_ status. The command attempts to search for the import ID 
in each failed jobs stored in the `failed_jobs` table on the database.
