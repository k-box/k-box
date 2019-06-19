# Import Users in batch and assign them to projects

Starting from K-Box version 0.7.0 there is the ability to perform user creation in batch, which means that is possible to creates a high number of users by using a file that describe the characteristics of those users.

The ability to perform the mass user creation is available only in the form of a maintenance command, therefore physical access to the running K-Box instance is required.

**Please use UTF-8 encoded CSV files**

## The Command

```shell
php artisan users:import [--delimiter=;] [--value-delimiter=,] {file}
```

**When run the login with an administrator account will be asked.**

### Parameters

- `{file}`: the path (with filename and extension) of the CSV file that contains the users to create. This parameter is required

### Options

This options are optional, if not specified the default values will be used.

- `delimiter`: The delimiter used in the CSV file for separating columns (e.g. ",", ";",...). The default delimiter is `;`
- `value-delimiter`: The delimiter used to separate values in a single column (e.g. ",", ":",...). The default value is `,`


## File Format

The command can understand only **Comma Separated Value (CSV)** files. Therefore the file must be a UTF-8 encoded textual file with content formatted as

- first line is the header
- second to last line are the users
- users' data is separated in columns

The next block shows an example CSV file content that is understood by the command.

```
User;Email;Role;Manage Project;User of project
user-1;user-1@k-link.technology;Partner;;test
user-2;user-2@k-link.technology;Guest;;
user-3;user-3@k-link.technology;ProjectAdmin;lead by;test,secondary
user-4;user-4@k-link.technology;K-Linker;;panco,pillo,pluto
user-5;user-5@k-link.technology;Admin;;
user-6;user-6@k-link.technology;;Cannot Be PrjAdmin;Another Project
user-7;user-7@k-link.technology;;;Another Project
```

### Columns

Column names can be lower case, upper case or mixed.

Columns must be in the following order (otherwise an [error](#general-errors) will be showed)

1. `User`: the user name
2. `Email`: the user email address
3. `Role`: The user role [see Acceptable Role values](#acceptable-role-values)
4. `Manage Project`: the project list he will manage, projects must be expressed with their names and they must exists before the procedure is launched
5. `User of Projects`: the project list he needs to have access to, projects must be expressed with their names and they must exists before the procedure is launched

Column 4 and 5 can be omitted if the users don't have to manage existing projects or don't have to be added to existing projects.

To make column headers a little flexible the columns are considered valid if the column name is included in the acceptable list of column aliases. Also column names could contain phrases in brackets. For example a column named like this `User (Name, Last Name)` in the file will be considered as it is composed only by the word `User`.

#### List of columns with acceptable aliases

1. `User`: `user`
2. `Email`: `email`, `mail`
3. `Role`: `role`
4. `Manage Project`: `manage project`, `manage`, `manage-projects`, `manage-project`
5. `User of Projects`: `user of project`, `user of projects`, `add to`, `projects`

The column name comparison is performed in a case insensitive way, which means that `User == user == USER == uSer == useR` and so on


### Acceptable Role values

The role column must contain a particular string in order to be understood by the command, acceptable values are here listed:

- `partner`: the user will have a Partner account
- `projectadmin` or `project-admin`: the user will manage projects
- `admin`: the user will be a K-Box administrator

An empty role value means `partner`.


## Errors and other messages

When executing the command a set of errors can be raised and showed. All parameters are evaluated before actually creating users

**Please note that Project names are case sensitive, make sure to write project names exactly.**

The following error messages can be showed:

#### General errors

- `Not enough arguments (missing: "file").`: the CSV file has not been specified
- `The file ./data/totally-non-existing-file.csv cannot be found or readed`: the command cannot find the file or has not reading permission
- `Wrong column name, expecting manage project or manage or manage-projects or manage-project found k-linker at index 3` one of the column in the first line is not specified as expected

This general errors appears in the form of a single message, like

```

  [RuntimeException]
  Not enough arguments (missing: "file").

```

#### Errors tied to the user's parameters

- `The selected manage projects is invalid.`: One of the project names listed in the *Manage Projects* column do not exists
- `The selected add to projects is invalid.`: One of the project names listed in the *Add to Projects* column do not exists
- `The selected role is invalid.`: The role value is not in the acceptable list of role values or the role is not compliant with the value specified in the *Manage Projects* column. In other words you have stated that the user should manage a project, but the role is not *projectadmin* or *admin*
- `The email has already been taken.` A user with the same email address already exists
- `The email must be a valid email address.` The email address is not formatted like a real email address, format: `user@something.com`

This errors refers to a line in the CSV file, therefore they appear in a tabular fashion, like

```
+----------+-------------------+----------+--------------------+-------------------+------+---------------------------------------------------------------------------------------------------------------------+
| username | email             | role     | manage_projects    | add_to_projects   | line | error                                                                                                               |
+----------+-------------------+----------+--------------------+-------------------+------+---------------------------------------------------------------------------------------------------------------------+
| user-4   | user-4@k-link.technology | k-linker |                    | panco,pillo,pluto | 4    | The selected add to projects is invalid.                                                                            |
| user-6   | user-6@k-link.technology | partner  | Cannot Be PrjAdmin | Another Project   | 6    | The selected role is invalid. - The selected manage projects is invalid. - The selected add to projects is invalid. |
| user-7   | user-7@k-link.technology | partner  |                    | Another Project   | 7    | The selected add to projects is invalid.                                                                            |
+----------+-------------------+----------+--------------------+-------------------+------+---------------------------------------------------------------------------------------------------------------------+
```

All the information about the line that raised the error are showed.
