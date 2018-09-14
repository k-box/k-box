<div id="map" style="width: 100%;height: 100%;">


</div>

<script>
	require(['modules/leaflet'], function(L){

        L.Icon.Default.prototype.options.imagePath = "/images/";

        var defaultBaseMap = L.tileLayer('https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
                id: "hot-osm",
                maxZoom: 20,
                subdomains: "abc",
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>'
            });

        var file = L.tileLayer.wms('{{ $wmsBaseUrl }}', {
            id: "my",
            format: 'image/png',
            transparent: true,
            maxZoom: 54,
            // L.CRS.EPSG3857 is automatically set as CRS, as the map instance is configured with that
            minZoom: 0,
            styles: '{{ $styles }}',
            version: '1.1.1',
            layers: "{{ $layers }}",
            attribution: '{{ $attribution }}'
        });

        var baseMaps = {
            "OpenStreetMaps": L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                id: "osm",
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }),
            "Humanitarian OpenStreetMaps": defaultBaseMap,
            "Mundialis (Topographic OSM)": L.tileLayer.wms('http://ows.mundialis.de/services/service?', {
                id: "mun",
                layers: "TOPO-OSM-WMS",
                attribution: '&copy; <a href="https://www.mundialis.de/en/ows-mundialis/" target="_blank">mundialis GmbH & Co. KG</a>'
            })
        };

        var overlayMaps = {
            "{{$file->name}}": file
        };
        
        var map = L.map('map', {
            crs: L.CRS.EPSG3857, // spherical mercator projection https://leafletjs.com/reference-1.3.4.html#crs
            zoom: {{ $mapZoom ?? 11 }},
            layers: [defaultBaseMap, file],
        });

        @if(isset($mapBoundings) && !empty($mapBoundings))
            map.fitBounds({{$mapBoundings}});
        @else 
            map.setView({{ $mapCenter ?? "[52.5200, 13.4050]" }});
        @endif

        L.control.layers(baseMaps, overlayMaps).addTo(map);

        window.preview = map;
	});
</script>
