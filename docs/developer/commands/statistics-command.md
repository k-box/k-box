# `statistics` command

Generate usage report for

- Documents created per day (not considering versions)
- File uploads per day
- Network publications per day
- Users creation per day
- Projects creation per day
- Collections creation per day
- Personal collections creation per day

The report is written in the K-Box storage folder in CSV format.

```
$ php artisan statistics [options]
```

**Options:**
- `--days` Specify how many days should be taken into consideration for the report. Default 30
- `--summary` Prints a summary of the K-Box usage
- `--overall` In conjunction with `--summary`, prints an overall summary of the K-Box usage since the first day. This will not take into consideration the `days` option
- `--influx` In conjunction with `--summary` prints the usage statistics according to the InfluxDB Line Protocol
  

### Examples

#### Print overall K-Box usage summary

```
php artisan statistics --summary --overall --influx
```
```
+--------------------------------------+----------------+
| Documents (not considering versions) | 8              |
| Uploads                              | 10             |
| Published documents                  | 1              |
| Registered users                     | 3              |
| Projects                             | 2              |
| Collections                          | 7              |
| Personal collections                 | 3              |
| Overall searches                     | 4              |
| Most used search keyword             | hello, 2 times |
+--------------------------------------+----------------+
```

#### Print overall K-Box usage summary following Influx format

```
php artisan statistics --summary --overall --influx
```

```
kbox,domain=http://localhost:8000 documents=8i,uploads=10i,published=3i,users=1i,projects=2i,collections=7i,personal_collections=3i
```
