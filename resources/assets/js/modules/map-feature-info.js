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
                    var text = this.parseFeatureInfo(result, url);
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

            parseFeatureInfo: function (result, url) {

                if(!result.type){
                    return JSON.stringify(result);
                }
                
                if(result.type !== "FeatureCollection"){
                    return JSON.stringify(result);
                }

                if(result.features && result.features.length > 0){
                    return JSON.stringify(result);
                }

                return null;
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
