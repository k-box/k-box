---
Title: Restore backup
Description: How to restore a K-Box backup
---

Assuming that you have a full backup, as presented in the [backup section](./backup.md), 
restoring it means create a new K-Box installation and place the storage and configuration options 
in the correct place.

**notices**

- If you used a tool to create backup archives, like Restic, please ensure that the data are available
and can be copied on the machine.
- Make sure to use the same K-Box version as the one that was running at the time of the backup
- Restore a backup on the same domain as it was created on. Transfer of domain is not supported and must 
  be done manually. See [Transfer to another domain](./transfer-to-another-domain.md) for further information

**before start**

- Ensure that the server/machine you are restoring the backup on meet the [installation requirements](../installation/requirements.md).
- Ensure that the backup contains at least
  - a Docker compose file, e.g. `docker-compose.yml`, and 
  - two folders `database` and `data`
  - in some configurations an optional `kbox.env` could be also present
- Ensure that you can manage the domain under which the K-Box was previously available

### Preparing to restore the backup

Create a folder for the configuration and a folder structure based on 
the volumes configuration in the `docker-compose.yml` file.

The volume folders can be found in the docker-compose.yml in 
the volume configuration for each service. Volumes could be
defined on separate partitions or in the same directory
as the configuration file.

```yaml
# Case 1, storage on separate partition
volumes:
 - "/mnt/data:/var/www/dms/storage"

# Case 2, storage inside the same directory as the configuration
volumes:
 - "./storage/k-box/database:/var/lib/mysql"
```

Proceed to create the same folder structure as presented in the docker compose file 
or change the local folder for the volume configuration according to your needs. 

Usually the folder names have this meaning:

- `data`, the storage directory of the K-Box (`/var/www/dms/storage` inside the `kbox` container)
- `database`, the storage directory of the MySql/MariaDB database (`/var/lib/mysql` inside the 'database` container)
- `k-search`, the storage directory for the Apache Solr index (`/opt/solr/k-search/k-search/data` inside the `solr` container)

Now transfer the backup content into the created folder structure.

> Change of file owner or group owner might be necessary


### Pulling the docker images

Pull the docker images by executing the following command

```
docker-compose pull
```

### Reverse proxy

If the K-Box was deployed behind a reverse proxy, the configuration might contain additional lines. 
For example the Traefik configuration could appear as follows:

```yml
labels:
 - "traefik.enable=true"
 - "traefik.frontend.rule=Host: my.domain.com"
 - "traefik.docker.network=reverseproxy_web"
```

Before starting up the K-Box ensure that the [proxy service is properly configured](../installation/reverse-proxy.md). 


### Start-up

Now the K-Box can be start.

```
docker-compose up -d
```

For K-Box startup problems and errors check the logs using `docker-compose logs --follow kbox`, 
or `docker-compose logs --follow` for all services. 
See [logging](./logging.md) for an comprehensive presentation on the K-Box logs.

If the startup procedure complete without errors you can browse the K-Box at its original domain.

### Configuration and additional steps

#### Document Search

Ensure that the full text search index is up-to-date by running

```
docker-compose exec kbox php artisan dms:reindex --only-private
```

> This operation might require several hours to complete depending on how many files are in the K-Box

#### Mail Configuration

E-Mail configuration can be omitted under some circumstances from the backup. 
If you don't see any email related configuration please [configure your own SMTP server](../administration/mail.md).

A valid email configuration is required for the K-Box to operate.

#### K-Link Connection

If the K-Box was connected to a K-Link please ensure that the K-Link is already available.
If that cannot be guaranteed, disable the K-Link connection to prevent errors.

In any case review any [K-Link related settings](../administration/network.md).

After a succesfull connection you might want to reindex all published documents

```
docker-compose exec kbox php artisan dms:reindex --only-public
```

> See [`dms:reindex` command](../developer/commands/reindex-command.md) for additional options

#### Plugins

K-Box plugins might be disabled after a backup restore. 
If that is the case proceed to [enable](../developer/plugins/plugins.md) and configure them from the administration user interface.


## Additional resources

- [Transfer to another domain](./transfer-to-another-domain.md)
