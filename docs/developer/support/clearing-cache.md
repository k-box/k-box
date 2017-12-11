# Clearing cache

The Cache is used to speed up the K-Box pages visualization. Caching time can differ from 1 minute to 1 hour.

If a user is experiencing problems with:

- Project lists not refreshing after being joined into a project
- Collections list not refreshing after create, edit, delete
- Microsite not refreshing

is highly probable that the cache has not be updated for the specific user and so he/she is seeing an old version.

To force a clear of the cache you need to connect to the K-Box Docker container, navigate to `/var/www/dms` and issue the following command

```
> php artisan cache:clear
```

This command will clear all the cache files. The first page reload of the K-Box after the execution of this command can be slower. 

If the user is still experiencing problem after a cache refresh fill an issue with all the details to reproduce the problem.

