# `create-admin` command

Create a K-Box administrator account.

```
$ php artisan create-admin [options] {email}
```

**Options:**

- `--password`: Specify the password on the command line. If omitted the password will be asked using an interactive input
- `--no-interaction`: Do not ask interactive questions. In this case a password reset link will be printed
- `--show`: Shows the generated password, only if `--password` is omitted
- `--name` : The name to assign to the user. By default is the email user name

**Arguments**

- `email` (required): The email address of the user


**Error codes**

The command might return the following exit codes in case of error

- `1` in case of general error
- `2` in case a user with the same email address already exists
- `3` in case the email address is not valid
