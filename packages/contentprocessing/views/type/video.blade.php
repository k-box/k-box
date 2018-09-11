<video id="the-player" 
    data-dash="{{ route('video.play', ['uuid' => $file->uuid, 'resource' => 'mpd']) }}"
    data-source="{{ DmsRouting::download($document, $file) }}"
    data-source-type="{{ $file->mime_type }}"
    controls preload="none"
    poster="{{ DmsRouting::thumbnail($document, $file) }}">
</video>