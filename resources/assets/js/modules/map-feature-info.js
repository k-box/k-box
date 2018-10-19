define("modules/map-feature-info", ["jquery"],
    function ($) {

        // This module add the option to interact with the WMS GetFeatureInfo endpoint by listening and reacting to map events.
        // to initialized it call the init function with the necessary options
        // Leaflet and the map must be loaded prior to this module

        // Code is highly inspired from
        // https://gist.github.com/rclark/6908938 Ryan Clark
        // https://github.com/heigeo/leaflet.wms/ Copyright (c) 2014-2016 Houston Engineering, Inc.

        function ajax(url, callback){
            $.ajax({
                url: url,
                success: function (data, status, xhr) {
                  callback.call(internal, data, null);
                },
                error: function (xhr, status, error) {
                  callback.call(internal, null, { error: error, body: xhr.responseBody});
                }
          
              });
        }


        var internal = {
            _L: null,
            _map: null,
            _layer: null,
            _url: null,
            _popup: null,

            handleMapClick: function (evt) {
                this.getInfo(evt.latlng, this._layer);
            },

            getInfo: function (latlng, layers) {
                var params = this.buildParameters(latlng, layers);
                var url = this._url + L.Util.getParamString(params, this._url);

                ajax(url, done);

                function done(result, error) {
                    if(error){
                        return;
                    }
                    var text = this.parseFeatureInfo(result, latlng);
                    if(text){
                        this.show(latlng, text);
                    }
                }
            },

            buildParameters: function (latlng, layer) {
                var point = internal._map.latLngToContainerPoint(latlng, internal._map.getZoom());
                var size = internal._map.getSize();
                
                var params = {
                    request: 'GetFeatureInfo',
                    service: 'WMS',
                    srs: 'EPSG:4326',
                    styles: layer.wmsParams.styles,
                    transparent: layer.wmsParams.transparent,
                    version: layer.wmsParams.version,
                    format: layer.wmsParams.format,
                    layers: layer.wmsParams.layers,
                    query_layers: layer.wmsParams.layers,
                    info_format: 'application/json',
                    bbox: internal._map.getBounds().toBBoxString(),
                    height: size.y,
                    width: size.x,
                    exceptions: "application/json"
                };

                params[params.version === '1.3.0' ? 'i' : 'x'] = point.x;
                params[params.version === '1.3.0' ? 'j' : 'y'] = point.y;

                return params;
            },

            parseFeatureInfo: function (result, latlng) {

                if(!result.type || (result.type && result.type !== "FeatureCollection")){
                    return null;
                }

                if(result.features && result.features.length === 0){
                    return null;
                }

                var features = [];

                $.each(result.features, function(index, feature){
                    features.push(this.renderFeatureDetails(feature));
                }.bind(this));

                features.push('<div class="map__feature" title="Latitude and Longitude of the location you clicked on the map"><span class=""><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 14c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.103 0 2 .897 2 2s-.897 2-2 2-2-.897-2-2 .897-2 2-2z"/><path d="M11.42 21.814a.998.998 0 0 0 1.16 0C12.884 21.599 20.029 16.44 20 10c0-4.411-3.589-8-8-8S4 5.589 4 9.995c-.029 6.445 7.116 11.604 7.42 11.819zM12 4c3.309 0 6 2.691 6 6.005.021 4.438-4.388 8.423-6 9.73-1.611-1.308-6.021-5.294-6-9.735 0-3.309 2.691-6 6-6z"/></svg></span> ' + (Math.round(latlng.lat * 100000000)/100000000) + ', ' + (Math.round(latlng.lng * 100000000)/100000000) +'</div>');
                
                return '<div class="map__features">'+features.join('')+'</div>';
            },

            renderFeatureDetails: function(feature){
                if(feature.properties.GRAY_INDEX){
                    return '<div class="map__feature"><span class="map__feature-label">value</span> <strong>' + feature.properties.GRAY_INDEX + '</strong></div>';
                }

                var properties = [];
                $.each(feature.properties, function(label, property){
                    if(property){
                        var value = "";
                        
                        if($.isArray(property) || $.isPlainObject(property)){
                            value = JSON.stringify(property);
                        }
                        else if($.isNumeric(property)){
                            value = property;
                        }
                        else {
                            value = property.replace(/\\n/gi, "<br/>");
                        }
                        
                        properties.push('<div class="map__feature"><span class="map__feature-label">' + label + '</span> ' + value + '</div>');
                    }
                }.bind(this));

                return properties.join('');
            },

            show: function (latlng, info) {
                
                if (!internal._map) {
                    return;
                }
                
                internal._L.popup().setLatLng(latlng).setContent(info).openOn(internal._map);
            },
        };


        var module = {

            init: function (L, map, layer) {
                internal._map = map;
                internal._L = L;
                internal._layer = layer;
                internal._url = layer._url;
                map.on('click', internal.handleMapClick, internal);
            }
        }

        return module;
    });
