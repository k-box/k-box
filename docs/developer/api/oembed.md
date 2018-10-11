# oEmbed

K-Box supports oEmbed, a clever system for making it easy to embed rich content into websites. 
An example of oEmbed is when you drop a YouTube video URL on its own line in (say) a blog post, and it will get replaced with an actual embedded video.

This is also how K-Box oEmbed works, just drop a document preview URL into the content area of an oEmbed-enabled site and it will be converted into an Embedded Preview automatically.

## How It Works

The oEmbed spec is pretty simple: an endpoint that accepts one URL parameter: another URL.

The K-Box expose this endpoint on `/api/oembed`:

```
https://instance.k-box.net/api/oembed?url=URL-TO-DOCUMENT-HERE
```


If `URL-TO-DOCUMENT-HERE` is a valid Document preview URL, it will return a JSON response, like:

```json
{
  type: "rich",
  version: "1.0",
  provider_name: "K-Box",
  provider_url: "http://instance.k-box.net",
  title: "Climate change resilience in rural areas",
  height: "480",
  width: "360",
  html: "<iframe width=\"480\" height=\"360\" src=\"http://instance.k-box.net/d/embed/UUID\" class=\"kbox_embed_iframe\" frameborder=\"0\" allowfullscreen></iframe>"
}
```

We return a _"rich"_ content type, meaning supporting sites should use the HTML we return to display the content. That HTML, in our case, is an HTTPS `<iframe>` with the Embedded Preview. 
Sites that support oEmbed will take that HTML we return and drop it onto the page.


## HTTPS

You can make the oEmbed call over HTTP or HTTPS. Either way, the returned `iframe` will be HTTPS.


## Limitations

- We only allow `/d/show/` URL's.
- You can't customize the Embed style.


## Specifics about Using oEmbed in CMSs

Content Management Systems (CMS), like WordPress, support oEmbed. But they work off an internal "whitelist" of sites. 
If K-Box document URLs are not transforming into the Embedded Preview means that the CMS does not have the oEmbed URL whitelisted.

In WordPress adding oEmbed support for K-Box is a one liner in your theme's `functions.php` file:

```php
wp_oembed_add_provider('http://instance.k-box.net/d/show/*', 'https://instance.k-box.net/api/oembed');
```

Or perhaps even better, you can create a plugin to enable support. This is a good way to go, so that support persists no matter what theme you use.
