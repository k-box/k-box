# `appearance:downloadpicture` command

Download the [appearance](../../administration/appearance.md) picture, as defined in  defined in the `appearance.picture` configuration value.

The file is downloaded in the public storage directory (`storage/public/appearance`).

## Usage

```
$ php artisan appearacence:downloadpicture [--now] [--picture=file] [--force]
```

**options**

- `--now` immediately download the specified picture
- `--picture` the url of the picture to download, if not specified defaults to to the `appearance.picture` configuration value
- `--force` use it to force the download of an already downloaded image

