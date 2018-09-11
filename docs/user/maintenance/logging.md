---
Title: Logging
Description: How the K-Box logs what happens
---

## Where are the logs

The K-Box log events in two locations

1. The Docker logs
2. The application daily log

### Docker logs

The Docker containers can log information while running. Based on the installation using Docker, the containers write the log information directly to Docker.

To read the log entries, from the deployment folder, execute

```bash
docker-compose logs kbox
``` 

> There are more options that just see the full log list, check the [`logs` command documentation](https://docs.docker.com/compose/reference/logs/).

### Application log

The K-Box stores an additional daily log in the storage folder, inside a sub-folder called `logs`.

This log file is named according to the pattern `laravel-YEAR-MONTH-DAY.log`.

The log file, inside the Docker container, is located in `storage/logs`.

```bash
docker-compose exec kbox bash
# this will give you a shell inside the running container
ls storage/logs
# laravel-2018-03-01.log laravel-2018-03-02.log
cat storage/logs/laravel-2018-03-01.log
``` 

> **Note** you could also reach the log files by entering the storage location as configured during the installation.

> Log files older than 5 days might not be available.

## View application logs from the User Interface

The last 1000 lines of the current daily [application log](#application-log) can be viewed directly from the K-Box User Interface, from the  `Administration > Maintenance and Events` page. 

## Previous: [Overview](../intro-dev.md)
## Next: [Backup](./maintenance/backup.md)
