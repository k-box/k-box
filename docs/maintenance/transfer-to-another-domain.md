---
Title: Transfer a K-Box to another domain
Description: How to move the K-Box to another domain
---

To change the domain of K-Box the application url must be changed and all documents reindexed.

> If the K-Box is running, stop it and clean any docker container `docker-compose down` 
> (make sure data is persisted, otherwise you will loose it).

First change the application URL in the deploy configuration file (usually `docker-compose.yml`)
to the domain:

```yml
"KBOX_APP_URL=https://new.domain.com"
```

Now the K-Box can be restarted

```
docker-compose up -d
```

If the instance published documents to a K-Link a reindex is necessary.

> Ensure that the domain change is performed also at the K-Link Registry level, 
> otherwise all publication requests will be denied.

```
docker-compose exec kbox php artisan dms:reindex --only-public
```
