define("modules/spatial_filters", ["require", "modernizr", "jquery", "DMS", "modules/leaflet", "modules/leaflet-draw", 'language'], 
    function (_require, _modernizr, $, DMS, L, LDraw, Lang) {
    
	console.log('loading spatial-filters module...');



    function getParameterByName(name, url) {
        if (!url) url = window.location.search || window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    var _pageArea = $("#page");
    var _mapLoaded = false;
    var currentFilter = null;
    var otherFilters = null;

    var geojsonFeature = {
        "type": "Feature",
        "properties": {
            "name": "Coors Field",
            "amenity": "Baseball Stadium",
            "popupContent": "This is where the Rockies play!"
        },
        "geometry": {
            "type": "Point",
            "coordinates": [-104.99404, 39.75621]
        }
    };


    function applyFilter(bbox){

        var geoFilter = {geo_location: bbox};
        var filters = $.extend(geoFilter, otherFilters);

        DMS.navigate("geoplugin/documents", filters);
    }


    function loadMap()
    {
        L.Icon.Default.prototype.options.imagePath = "/images/";
        var map = L.map('js-spatialfilter', {
            crs: L.CRS.EPSG3857,
            zoom: 4,
            minZoom: 3,
            maxZoom: 20,
            // maxBounds: L.latLngBounds([[-180, 90], [180,-90]])
        })
        
        if(!currentFilter){
            map.setView([52.5200, 13.4050], 3);
        }
        else {
            map.fitBounds(currentFilter);
        }

        L.tileLayer("https://tile-{s}.openstreetmap.fr/hot/{z}/{x}/{y}.png", {
            maxZoom: 20,
            subdomains: "abc",
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors. Tiles courtesy of <a href="http://hot.openstreetmap.org/" target="_blank">Humanitarian OpenStreetMap Team</a>',
            keepBuffer: 4,
            noWrap: true
        }).addTo(map);

        var drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        var drawControl = new L.Control.Draw({
            position: 'topleft',
            draw: {
                polyline: false,
                polygon: false,
                circle: false,
                marker: false,
                circlemarker: false,
            },
            edit: {
                featureGroup: drawnItems,
                remove: true,
                edit: true
            }
        });

        map.addControl(drawControl);

        // L.geoJSON(geojsonFeature).addTo(map);

        map.on(L.Draw.Event.CREATED, function (e) {
            var type = e.layerType,
                layer = e.layer;
            // Do whatever else you need to. (save to db; add to map etc)
            drawnItems.addLayer(layer);
            console.log(layer.getBounds().toBBoxString());

            applyFilter(layer.getBounds().toBBoxString());
         });

         map.on(L.Draw.Event.DELETED, function(e){
            console.log(e);
         } );

        _mapLoaded = true;
    }

	var module = {

        init: function(options)
        {

            if(options){
                currentFilter = options.filter || null;
                otherFilters = options.otherFilters || null;

                console.log(options);
            }

            console.log("initializing map");
            
            _pageArea.on('spatialfilters:open', function(evt){
                
                if(!_mapLoaded){
                    loadMap();
                }
            });
            _pageArea.on('spatialfilters:close', function(evt){
                
            });
    
        },

    }

    window.SpatialFilters = module;
    
	return module;
});
