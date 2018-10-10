---
Title: Reverse proxy
Description: How to deploy the K-Box behind a proxy
---

The K-Box can be exposed behind a reverse proxy. It is actually suggested to do so, as the K-Box configuration do not natively support secure connections. 
In addition, you might have more services on your domain and on your server, therefore you might want to expose the K-Box on a path on on a sub-domain using the same server that is managing a website.

Different reverse proxies exists, [NGINX](https://www.nginx.com/), [HAProxy](www.haproxy.org/), [Traefik](https://traefik.io/), for naming a few. 
We selected Traefik for our example because it plays well with the Docker based installation, is Open Source and supports HTTPS certificate generation via [Let's Encrypt](https://letsencrypt.org/).

### Using Traefik

Considering the default [K-Box installation](./installation.md), to use Traefik as your reverse proxy, a few configurations are necessary.

#### Configuring an starting Traefik

As the first step Traefik needs to be installed on the server. For simplification purposes we will install Traefik on the same machine as the K-Box.

Let's create a folder, called `reverseproxy`, inside the `~/deploy/` folder used for the K-Box.

We will use Docker to start Traefik, so we create a `docker-compose.yml` file.

A Docker network, called `web`, is generated. The network will be used to connect each service that needs to be proxiesd. Then we expose the ports 80 and 443 on the `proxy` service. The port 443 will be used for secure connections.

```yml
# ~/deploy/reverseproxy/docker-compose.yml
version: '2'

networks:
  web:
    driver: "bridge"

services:
  proxy:
    image: "traefik:1.5"
    command: "--logLevel=ERROR"
    restart: always
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - "/var/run/docker.sock:/var/run/docker.sock:ro"
      - "./data/cert/:/cert/"
      - "./data/conf/:/etc/traefik/:ro"
    labels:
      - "traefik.enable=false" # set to true to expose Monitoring & API
      - "traefik.backend=proxy"
      - "traefik.port=8080"
    container_name: proxy
    networks:
      - "web"
```

Inside the docker-compose.yml file two volumes are defined so that Traefik can read its configuration and store the SSL certificates generated for each proxied host.

```
"./data/cert/:/cert/"
"./data/conf/:/etc/traefik/:ro"
```

> The Docker socket, `/var/run/docker.sock`, is mounted to let Træfik listen for events and automatically reconfigure. See [Docker & Traefik](https://docs.traefik.io/user-guide/docker-and-lets-encrypt/) for more information.

Once the two folders are created, we can add the Traefik configuration file, `traefik.toml`, in `./data/conf/traefik.toml`

A Traefik configuration file, with Let's Encrypt support, looks like the next code block.

```conf
# accept self-signed SSL certs for backends
InsecureSkipVerify = true

defaultEntryPoints = ["http", "https"]

[acme]
email = "your@email.net"
storage = "cert/acme.json"
entryPoint = "https"
onDemand = false
OnHostRule = true

[acme.httpChallenge]
entryPoint = "http"

[entryPoints]
  [entryPoints.http]
    address = ":80"
      [entryPoints.http.redirect]
        entryPoint = "https"
  [entryPoints.https]
    address = ":443"
      [entryPoints.https.tls]

[web]
# own web server address (displays statistics)
address = ":8080"

[docker]
endpoint = "unix:///var/run/docker.sock"
domain = "docker.local"
watch = true
exposedbydefault = false
```

> You must replace `your@email.net` (`[acme]` section) with a real email address.


After saving the configuration file the Traefik service can be started

```
cd ~/deploy/reverseproxy/
docker-compose up -d proxy
```

#### Update the K-Box installation configuration to use Traefik

Once the Traefik service is up and running, we can apply the required change to the K-Box docker-compose.yml configuration.

First an external network, called `web`, needs to be defined:

```yml
networks:
  internal:
+  web:
+    external:
+      name: reverseproxy_web
```

Second, for each service that should be accessible from outside we need to add the `web` network and the Traefik configuration:

```yml
kbox:
  # ...
  networks:
    - internal
+    - web
+  labels:
+    - "traefik.enable=true"
+    - "traefik.frontend.rule=Host: my.box.tld"
+    - "traefik.docker.network=reverseproxy_web"
```

The `traefik.frontend.rule=Host: my.box.tld` tells Traefik that the service will be reachable if the user issue a request to my.box.tld, using http or https, as defined by Traefik listening on port 80 and 443.

Third, the exposed ports needs to be removed:

```yml
-    ports: 
-      - "8080:80"
```

After changing the configuration you should restart the K-Box service so Traefik will be notified. At this point Traefik will take care of generating the SSL certificate.
