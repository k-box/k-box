# import

**The folder import cannot take folders from your local file system. The folder that can be used for import must be reachable from the server. If you want to import all the files in a folder on you file system please use the standard upload (the Chrome browser supports also the drag and drop of folders).**


The import procedure could be quite tricky depending on your system configuration.

The DMS is written in PHP so access to network share are under the control of Apache and PHP, hence the installation might not have the required permission to open network shares or the php configuration will block access to folders outside the php environment or the user on which the php and apache process are run does not have the permission to access network folders and so on... (technically a mess).

Sometimes on the system (if linux based) is available the `smbclient` which will give access to the network share, but at the time of writing the DMS does not interact with such executable.

Let's go straight to the point. **To access network shares you have to mount them as local drives** then you can access the folders from php.

for example from a terminal/shell

```
mount -t smbfs //Network/share /mnt/path
```

make sure that `/mnt/path` is an existing folder on your system
