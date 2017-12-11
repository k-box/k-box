# K-Link K-Box 
# Developer documentation


## Introduction

...


## Configuration

See [Configuration.md](./configuration.md)


## Command Line

The K-Box exposes a set of commands through a command line interface in addition to 
the ones available from the framework used for developing the K-Box itself.

The command line tool set is exposed using 
[Laravel's Artisan](https://laravel.com/docs/5.2/artisan) command line.

See the section [Commands](./commands/index.md) for more information. 



## Support and Maintenance

Sometimes a support request is about a user that is experiencing problems during the normal operations on the K-Box. 

From the perspective of the support different actions can be made. Here is the list of the operations that can be performed:

- [View the current log file](./support/view-logs.md)
- [Clear the K-Box cache](./support/clearing-cache.md) (_requires physical access_)
- [Transfer Project ownership to another Project Administrator](./support/transfer-project-ownership.md)  (_requires physical access_)


### First step of a technical support request solving

The very first step of solving each support request is gather the largest amount of information from the user about the action he/she has performed. This includes: 

- visited pages
- the context information reported in the ticket request
- user ID
- collections identifiers (if needed)
- document identifiers (if needed)
- log entries that are around the time of the problem

Sometimes log entries reveal the nature of the error showed to the user, sometimes not. In the latter case assign the ticket to a developer.


## Testing

### Automatic tests

After each code push to git.klink.asia the system will automatically execute a syntax check over every PHP file.

### Manual triggered tests

- [Unit Tests](./testing/unit-tests.md)
- [Test Instance](./testing/test-instance.md)
