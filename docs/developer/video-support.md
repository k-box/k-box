# Video Support

The K-Box supports the upload of video files. For selected file types the K-Box offers streaming playback and advanced features.

## Supported video formats

- mp4 video encoded with h.264 codec, AAC or MP3 audio. Minimum resolution 480x360 pixel

## Playback

Video playback can use lot of resources, especially for high resolution videos. For the supported video formats the playback use the [Dynamic Adaptive Streaming over HTTP](https://en.wikipedia.org/wiki/Dynamic_Adaptive_Streaming_over_HTTP) (DASH) protocol.

The DASH protocol enables players to switch the video quality of the playback according to bandwith and connection type. Currently this operation is done automatically by the player, like the Auto mode on Youtube.

**Supported browsers**

- Internet Explorer 11
- Microsoft Edge
- Firefox 49+
- Chrome 62+
- Safari 10.1 (on Mac OS)
- Chrome (latest) on Android 
- Firefox (latest) on Android
- Android stock browser on Android 5.0+

**Unsupported browsers**

For the unsupported browsers the original video file will be served. The list of unsupported browsers currently include Safari on iOS and Internet Explorer 10 (or below).

## Elaboration

Video elaboration is realized using the [OneOffTech Video Processing CLI](https://github.com/OneOffTech/video-processing-cli/).

For more information please refer to the [Video Processing CLI documentation](https://github.com/OneOffTech/video-processing-cli/#video-processing).
