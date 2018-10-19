# Tus based resumable upload

## Architecture

The tus server and clients comes from https://github.com/OneOffTech/laravel-tus-upload

The package exposes a Gate, called `upload-via-tus` to which the upload request is passed for getting approval. The Gate implementation is in the `AuthServiceProvider`.

Once the request is authorized the control passes to the tusd server, started by `php artisan tus:start`. The server will handle the upload from the client and report various events.
The handled events are (1) `TusUploadStarted` (by `TusUploadStartedHandler`) and (2) `TusUploadCompleted` (by `TusUploadCompletedHandler`). Respectively when the upload started and when the upload finished. During the started event handling, the `File` and the `DocumentDescriptor` entries are created. Those entries are created to track the upload progress and the asynchronous process that will happen after the upload finishes. The complete handler tracks the status of the upload and triggers the indexing pipeline in the search engine.

## Starting the Tus server

If you are **on** a **linux** system you can start the tus server directly with

```
php artisan tus:start
```

if You are using the Docker image of the K-Box you need to add

```yaml
kbox_tus:
  image: "kbox"
  links:
    - mariadb:mariadb
  volumes_from:
    - kbox_base
  working_dir: /var/www/dms
  command: tusd
```

to the docker-compose.yml file, like highlighted in the [docker-compose.example.yml](../../docker-compose.example.yml) in the root of the project repository.

### Development notices

While developing outsite the Docker image or not behind a proxy the tusd server can be started with the default options on a specific port on localhost. 

From the project root

```
php artisan tus:start
```

If you are planning to start tus behind a proxy make sure the tus base_path and the location alias in the proxy matches.

```conf
# in the .env file
TUSUPLOAD_HTTP_PATH=/tus-uploads/
```

```conf
## in the NGINX configuration
location ~* /tus-uploads {

	    # Disable request and response buffering
        proxy_request_buffering  off;
        proxy_buffering          off;
        proxy_http_version       1.1;

        # Add X-Forwarded-* headers
        proxy_set_header Host $http_host;
        proxy_set_header X-Forwarded-Host $http_host;
        proxy_set_header X-Forwarded-Proto $scheme;

        proxy_set_header         Upgrade $http_upgrade;
        proxy_set_header         Connection "upgrade";

        client_max_body_size     0;

        proxy_pass http://tusd:1080;
    }
```


