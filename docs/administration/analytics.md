---
Title: Analytics
Description: How to configure analytics tracking services
---
# Analytics

The K-Box can support analytics tracking for evaluating page views and search.

Currently the K-Box offers out-of-the-box support for [Matomo](https://matomo.org/) and [Google Analytics](https://analytics.google.com).

> **When activating the analytics service you should include the information of the service and the collected data inside the Privacy Policy**.

> To respect user privacy the analytics code is included only for logged-in users that gave consent to statistics collection.


## Configuration via User Interface

The analytics configuration is available via the _Administration > Analytics_ page.

The page present you the analytics providers and the configuration options.


## Configuration via deployment environment file

The K-Box also supports the ability to configure the analytics parameters at deploy time, i.e. when the K-Box is deployed.

The general configuration variables are:

- `KBOX_ANALYTICS_SERVICE`: The analytics tracking provider. Possible values are: `matomo`, `google-analytics`
- `KBOX_ANALYTICS_TOKEN`: The analytics token to use for identifying the visits to the K-Box installation

Based on the analytics service selected, more configuration options might be available.

**Matomo**

The Matomo analytics service require additional parameters:

- `KBOX_ANALYTICS_MATOMO_DOMAIN`: the domain on which the matomo instance is running, e.g. `https://matomo.org`
