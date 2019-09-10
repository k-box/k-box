<video id="the-player" 
    @if($file->videoResources()->has('dash') && $file->videoResources()->get('dash') !== null)
        data-dash="{{ route('video.play', ['uuid' => $file->uuid, 'resource' => 'mpd']) }}"
    @endif
    data-source="{{ DmsRouting::download($document, $file) }}"
    data-source-type="{{ $file->mime_type }}"
    controls preload="none"
    data-type="video"
    poster="{{ DmsRouting::thumbnail($document, $file) }}">
</video>