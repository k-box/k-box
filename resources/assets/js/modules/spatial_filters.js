define("modules/spatial_filters", ["require", "modernizr", "jquery", "DMS", "modules/leaflet"],
    function (_require, _modernizr, $, DMS, L) {
    
    console.log('loading spatial-filters module...');
    
    var _pageArea = $("#page");
    var _mapLoaded = false;
    var currentFilter = null;
    var otherFilters = null;
    var searchTerms = null;

    function applyFilter(bbox){

        var geoFilter = {geo_location: bbox};
        var preExisting = $.extend(otherFilters, searchTerms);
        var filters = bbox ? $.extend(geoFilter, preExisting) : preExisting;

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
            maxBounds: L.latLngBounds([[-180, 90], [180,-90]]),
            maxBoundsViscosity: 0.8
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
        
        if(currentFilter){
            var rectangle = L.rectangle(currentFilter, {weight: 1})
            drawnItems.addLayer(rectangle);
        }

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

        map.on(L.Draw.Event.CREATED, function (e) {
            var layer = e.layer;

            drawnItems.addLayer(layer);

            applyFilter(layer.getBounds().toBBoxString());
        });

        map.on(L.Draw.Event.EDITSTOP, function (e) {
            applyFilter(drawnItems.getBounds().toBBoxString());
        });

        map.on(L.Draw.Event.DELETED, function(e){
            applyFilter();
        } );

        _mapLoaded = true;
    }

    var module = {

        init: function(options)
        {

            if(options){
                currentFilter = options.filter || null;
                otherFilters = options.otherFilters || null;
                searchTerms = options.searchTerms || null;
            }

            console.log("initializing map");
            
            _pageArea.on('spatialfilters:open', function(evt){
                
                if(!_mapLoaded){
                    _require(["modules/leaflet-draw"], function(){
                        loadMap();
                    });
                }
            });
            _pageArea.on('spatialfilters:close', function(evt){
                
            });
    
        },

    }

    window.SpatialFilters = module;
    
    return module;    
});
