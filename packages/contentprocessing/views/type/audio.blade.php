<audio 
    id="the-player" 
    data-source="{{ DmsRouting::download($document, $file) }}"
    data-source-type="{{ $file->mime_type }}"
    controls preload="none"
    data-type="audio"
    poster="{{ DmsRouting::thumbnail($document, $file) }}">
    <source src="{{ DmsRouting::download($document, $file) }}" type="{{ $file->mime_type }}" />
</audio>