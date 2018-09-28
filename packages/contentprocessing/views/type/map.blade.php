<div id="map" style="width: 100%;height: 100%;">

</div>

<script>
	require(['modules/leaflet'], function(L){

        L.Icon.Default.prototype.options.imagePath = "/images/";

        var baseMaps = {

            @foreach ($providers as $providerId => $provider)

                "{{ $provider['label'] ?? $providerId }}" : L.tileLayer{{ $provider['type'] === 'wms' ? '.wms' : '' }}("{{$provider['url']}}", {
                    id : "{{ $providerId }}",
                    @if(isset($provider['maxZoom'])) maxZoom: "{{$provider['maxZoom']}}", @endif
                    @if(isset($provider['layers'])) layers: "{{$provider['layers']}}", @endif
                    @if(isset($provider['subdomains'])) subdomains: "{{$provider['subdomains']}}", @endif
                    attribution: '{!! $provider['attribution'] ?? '' !!}',
                }),
    
            @endforeach

        };

        var defaultBaseMap = baseMaps['{{$defaultProvider}}'];

        var file = null;

        @if(isset($geojson) && $geojson)
            file = L.geoJSON({!! $geojson !!});
        @elseif(isset($geoserver) && $geoserver)
            file = L.tileLayer.wms('{{ $geoserver }}', {
                id: "my",
                format: 'image/png',
                transparent: true,
                maxZoom: 18,
                // L.CRS.EPSG3857 is automatically set as CRS, as the map instance is configured with that
                minZoom: 0,
                styles: '{{ $styles }}',
                version: '1.1.1',
                layers: "{{ $layers }}",
                attribution: '{{ $attribution }}'
            });
        @endif

        var overlayMaps = file ? {
            "{{$file->name}}": file
        } : {};

        var layers = file ? [defaultBaseMap, file] : [defaultBaseMap];
        
        var map = L.map('map', {
            crs: L.CRS.EPSG3857, // spherical mercator projection https://leafletjs.com/reference-1.3.4.html#crs
            zoom: {{ $mapZoom ?? 11 }},
            layers: layers,
            maxZoom: 18,
        });

        @if(isset($boundings) && !empty($boundings))
            map.fitBounds({{$boundings}}, {maxZoom: 18});
        @elseif(isset($geojson) && $geojson)
            map.fitBounds(file.getBounds());
        @else 
            map.setView({{ $center ?? "[52.5200, 13.4050]" }});
        @endif

        L.control.layers(baseMaps, overlayMaps).addTo(map);

        window.MapInstance = map;
	});
</script>
