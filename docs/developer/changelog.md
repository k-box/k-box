# Changelog entries

The [`CHANGELOG.md`](../../changelog.md) file track notable changes to
the K-Box. The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/).

Each entry, or bullet point, in the Changelog file is generated from a single 
data file in the `changelogs/unreleased` folder.
The file is expected to be a YAML file in the following format:

```yaml
title: "A new important feature"
issue: 100
merge_request: 101
author: @octocat
type: added
```

The `merge_request` value is a reference to a merge request that adds this entry, and the `author` key 
(format: `<GitHub username>`) is used to give attribution to community contributors. 
The `issue` field is the reference to the issue under which the change might have been discussed.
`author`, `issue` and `merge_request` are optional. 
The `type` field maps the [category of the change](https://keepachangelog.com/en/1.0.0/#how), 
valid options are: `added`, `fixed`, `changed`, `deprecated`, `removed`, `security`. Type field is mandatory.

Community contributors and core team members are encouraged to add their name to the author field. 

## What can be added as changelog entry?

- Any change that introduces a database migration **must** have a changelog entry;
- Security fixes **must** have a changelog entry, without `merge_request` value
  and with `type` set to `security`;
- Any user-facing change **must** have a changelog entry. This includes both visual changes 
  (regardless of how minor);
- Performance improvements **should** have a changelog entry;
- _Any_ contribution from a community member, no matter how small, **may** have
  a changelog entry regardless of these guidelines if the contributor wants one
  Example: "Fixed a typo on the search results page";
- Any docs-only changes **should not** have a changelog entry;
- Any change behind feature flag **may** have a changelog entry;
- Any developer-facing change (e.g., refactoring, technical debt remediation,
  test suite changes) **can** have a changelog entry. Examples: "Refactor to 
  use model factories", "Update to Laravel v7.x".

## Writing good changelog entries

A good changelog entry should be descriptive and concise. It should explain the
change to a reader who has _zero context_ about the change. If you have trouble
making it both concise and descriptive, err on the side of descriptive.

Use your best judgement and try to put yourself in the mindset of someone
reading the compiled changelog. Does this entry add value? Does it offer context
about _where_ and _why_ the change was made? Is the _end benefit_ to the user clear?

| **Bad** | **Good**|
|---------|---------|
| Starred documents order | Show a user's starred documents at the top of the "My Uploads" section |
| Copy (some text) to clipboard | Update the "Copy to clipboard" tooltip to indicate what's being copied |
| Improves CSS and HTML problems in details panel | Fix layout spacing in document's detail panel |
| Remove `null`s from version array | Fix 500 errors caused by trashed file in version list |


## How to generate a changelog entry

An Artisan command, `changelog`, is available to generate the changelog entry file
automatically.

Its simplest usage is to provide the value for `title`:

```bash
php artisan changelog 'Hey Jo, I added a feature to the K-Box!'
```

At this point the command would ask you to select the category of the change 
(mapped to the `type` field in the entry):

```plaintext
>> Please specify the category of your change:
1. New feature
2. Bug fix
3. Feature change
4. New deprecation
5. Feature removal
6. Security fix
```

The entry filename is based on the name of the current Git branch. If you run
the command above on a branch called `feature-hey-jo`, it will generate a
`changelogs/unreleased/feature-hey-jo.yml` file.

The command will output the path of the generated file and its contents:

```plaintext
create changelogs/unreleased/feature-hey-jo.yml
---
title: Hey Jo, I added a feature to the K-Box!
issue:
merge_request:
author:
type:
```


### Options

| Option            | Shorthand | Purpose                                                    |
| ------------------| --------- | -----------------------------------------------------------|
| `--force`         | `-f`      | Overwrite an existing entry                                |
| `--dry-run`       |           | Don't actually write anything, just print                  |
| `--issue`         | `-i`      | Set the issue number                                       |
| `--merge-request` | `-m`      | Set merge request ID                                       |
| `--author`        | `-u`      | Specify the author                                         |
| `--type`          | `-t`      | The category of the change, valid options are: `added`, `fixed`, `changed`, `deprecated`, `removed`, `security` |
| `--help`          | `-h`      | Print help message                                         |



#### `--force` or `-f`

Use **`--force`** or **`-f`** to overwrite an existing changelog entry if it
already exists.

```plaintext
$ php artisan changelog 'Hey Jo, I added a feature to the K-Box!'
error changelogs/unreleased/feature-hey-jo.yml already exists! Use `--force` to overwrite.

$ php artisan changelog 'Hey Jo, I added a feature to the K-Box!' --force
create changelogs/unreleased/feature-hey-jo.yml
---
title: Hey Jo, I added a feature to the K-Box!
issue: 2020
merge_request: 2021
author:
type:
```

#### `--merge-request` or `-m`

Use the **`--merge-request`** or **`-m`** argument to provide the
`merge_request` value:

```plaintext
$ php artisan changelog 'Hey Jo, I added a feature to the K-Box!' -m 2021
create changelogs/unreleased/feature-hey-jo.yml
---
title: Hey Jo, I added a feature to the K-Box!
issue:
merge_request: 2021
author:
type:
```

#### `--issue` or `-i`

Use the **`--issue`** or **`-i`** argument to provide the
`issue` value:

```plaintext
$ php artisan changelog 'Hey Jo, I added a feature to the K-Box!' -i 2020
create changelogs/unreleased/feature-hey-jo.yml
---
title: Hey Jo, I added a feature to the K-Box!
issue: 2020
merge_request: 
author:
type:
```

#### `--dry-run`

Use the **`--dry-run`** argument to prevent actually writing anything:

```plaintext
$ php artisan changelog --dry-run
create changelogs/unreleased/feature-hey-jo.yml
---
title: Added an awesome new feature to the K-Box
issue:
merge_request:
author:
type:
```

#### `--author` or `-u`

Use the **`--author`** or **`-u`** argument to fill in the `author` value:

```plaintext
$ php artisan changelog -u "octocat" 'Hey Jo, I added a feature to the K-Box!'
create changelogs/unreleased/feature-hey-jo.yml
---
title: Hey Jo, I added a feature to the K-Box!
issue:
merge_request:
author: @octocat
type:
```

#### `--type` or `-t`

Use the **`--type`** or **`-t`** argument to provide the `type` value:

```plaintext
$ php artisan changelog 'Hey Jo, I added a feature to the K-Box!' -t added
create changelogs/unreleased/feature-hey-jo.yml
---
title: Hey Jo, I added a feature to the K-Box!
issue:
merge_request:
author:
type: added
```

### History and Reasoning

Our `CHANGELOG` file was previously updated manually by each contributor that
felt their change warranted an entry. When two merge requests added their own
entries at the same spot in the list, it created a merge conflict in one as soon
as the other was merged. To reduce the merge conflicts not updating the 
changelog quickly became a practice. Creating a release then became a slow
process.

To reduce the impact on contributors, reviewers and on the release process
we [started brainstorming](https://github.com/k-box/k-box/issues/435).
The discussion lead to the current solution of one file per entry,
and then compiling the entries into the overall `CHANGELOG.md` file during the
release process.
