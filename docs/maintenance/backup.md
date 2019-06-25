---
Title: Backup
Description: How to perform a K-Box backup
---

The current backup procedure consists in manual copying the stored files, the configuration and the database to a separate storage location. The procedure is not automated.

> The following procedure depends on the deployment choices and assumes installations are performed on a Linux system (e.g. Debian, Ubuntu). This guide is a general reference.

### Prepare the backup location

Before creating a backup make sure that the selected location for the backup is accessible by the server/computer that is hosting the K-Box.

A backup location can be a network share or a USB Drive.

### Stop the K-Box

In order to do a full backup, the K-Box must not run, otherwise file corruption can happen.

Depending on the deployment procedure, the K-Box setup can be located in

1. in the User home directory 
- under `/home/{user}/deploy/k-box`, where `{user}` represents the system username
- under `/home/{user}/k-box`, where `{user}` represents the system username
2. in your folder of choice, e.g. `/opt/k-box` 

The K-Box uses Docker and Docker Compose. To shutdown, open a terminal and execute

```bash
cd $FOLDER
docker-compose stop
```

where `$FOLDER` is the absolute path to the folder containing the K-Box setup.

> on some old setups the configuration file might be called `docker-compose-kbox.yml`, therefore [`docker-compose -f docker-compose-kbox.yml`](https://docs.docker.com/compose/reference/overview/)` stop` must be used


### Backup the data

Once the K-Box has stopped, the files and configuration can be copied to the chosen backup location.

In the following a USB drive, accessible from the desktop in `/media/backup-disk/`, is considered to be the backup location.

To backup the data open a terminal

```bash
sudo cp -r $FOLDER /media/backup-disk/*/
```

where `$FOLDER` is the absolute path to the folder containing the K-Box setup, e.g. `/home/user/k-box`.

Depending on the deployment strategy the folder can contain:

1. Only the configuration files. In this case the data resides on a specific partition of the disk, like `/data` or `/mnt/data`;
2. The configuration files and a sub-folder, e.g. `storage`. In this case all the data might reside in the `storage` sub-folder.

If you are unsure where the data is really stored, you can open the `docker-compose.yml` (with `cat docker-compose.yml` on the terminal) file and identify the volumes sections.

The two cases are presented in the following code block

```yaml
# Case 1, storage on separate partition
volumes:
 - "/mnt/data:/var/www/dms/storage"

# Case 2, storage inside the same directory as the configuration
volumes:
 - "./storage/k-box/database:/var/lib/mysql"
```

In the former the absolute path `/mnt/data` means that data resides in a different location (e.g., a different hard drive). In that case such folder contents must be copied in the backup location as well.
Please be aware that, in a normal deployment, there are at least 3 volumes locations specified in the configuration file. One for the database, one for the storage and one for the search engine. Each entry might have a different path.

In the second case the volumes configuration starts with `./`, which means the current folder where the `docker-compose.yml` file reside. In this case the copy (`cp`) command issued earlier already copied all the necessary data.

### Restart the K-Box

Once the data copy is complete, the K-Box can be restarted.

```bash
cd $FOLDER
docker-compose up -d
```

where `$FOLDER` is the absolute path of the folder on disk that contains the K-Box setup.

> In case of outdated setups, the configuration file might be named `docker-compose-kbox.yml`. In such case [`docker-compose -f docker-compose-kbox.yml`](https://docs.docker.com/compose/reference/overview/)` up -d` must be used

> In case of updated setups, the K-Box should be directly restartable using  `systemctl start {k-box-service}`, where `{k-box-service}` is the name of the service registered at system startup for managing the K-Box startup/shutdown 

### High Availability

The backup time may vary depending on the amount of data and the writing speed in the backup location.

If uptime of the K-Box is a constraint, an incremental strategy can be followed. 

To this aim the copy (`cp`) command above described needs to be substituted with the [`rsync`](https://www.digitalocean.com/community/tutorials/how-to-use-rsync-to-sync-local-and-remote-directories-on-a-vps) command. It actually performs incremental copy by computing changed files only. Recommendation: take care to directly verify the command arguments instead of blindly replacing `cp` with `rsync`.

#### Hot and Cold backup strategy

In case

- the uptime of the K-Box is a strict constraint and
- the expected amount of data added between backups is more than 1GB

the _hot_ and _cold_ approach can be applied.

_Hot_ backup means that the data is copied while the application is running, while _cold_ means that the application is stopped while the data is being copied.

The data storage of the K-Box can be copied while the application is running.

Let's consider, as an example, a setup in which the files uploaded in the K-Box reside in `./storage/k-box/data`, while the database is in `./storage/k-box/database`.

The content of `./storage/k-box/data` can be copied to the backup location even if the application is running, because the changes to the folder happens mostly on upload of new files from the K-Box UI. On the contrary the database folder (`./storage/k-box/database`) cannot be copied during the application execution as doing so will increase the change of corrupted database, leading to the un-ability of succesfully restore of, for example, collections structure, users, shares.

A possible _hot_ and _cold_ flow could be the following:

1. copy, with `rsync`, the content of the storage folder
2. shutdown the K-Box
3. copy, with `rsync`, both the storage folder and the database folder
4. restart the K-Box

### Alternatives to `cp` and `rsync`

For incremental backups, encryption, or you want to try a different tool, we suggest [Restic](https://restic.net/).



## Additional resources

- [Restore a backup](./restore-backup.md)
