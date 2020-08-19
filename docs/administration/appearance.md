---
Title: Appearance
Description: personalize the look and feel of the K-Box
---
# Appearance

You can personalize the look and feel of the K-Box based.

The personalization is currently limited to:

- the picture on the login and registration pages;
- the background color, as replacement of the image, on the login and 
  registration pages.

## Configuration

The configuration can be done only via static configuration (i.e. environment variables)
and so require a K-Box restart.

The two variables that control the configuration are:

- `KBOX_APPEARANCE_PICTURE` the url of the image to show;
- `KBOX_APPEARANCE_COLOR` the background color to use.

The image and the background color appears only on large screens (i.e. above 
1024px wide).

The color must be specified in hex format, e.g. `#ff0000`. 

The image can be a url of a JPEG picture hosted on a third party service or
inside the K-Box (it must be public).
At startup the picture, if hosted on third party services, will be downloaded
and served directly by the K-Box to respect the user's privacy.

For the picture to be effective the size must be at least 1920 x 1080 pixels.
If configured the image has higher priority than the background color.

## Downloading a new image

Image download can be forced via the [`appearance:downloadpicture command`](../developer/commands/appearance-download-picture.md).
