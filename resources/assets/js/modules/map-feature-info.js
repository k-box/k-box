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
                // TODO: Check if possible to discard the event if outside the layer boundaries
                this.getInfo(evt.latlng, this._layer);
            },

            getInfo: function (latlng, layers) {
                var params = this.getFeatureInfoParams(latlng, layers);
                var url = this._url + L.Util.getParamString(params, this._url);

                ajax(url, done);

                function done(result, error) {
                    if(error){
                        return;
                    }
                    var text = this.parseFeatureInfo(result, url);
                    this.showFeatureInfo(latlng, text);
                }
            },

            getFeatureInfoParams: function (latlng, layer) {
                var point = internal._map.latLngToContainerPoint(latlng, internal._map.getZoom());
                var size = internal._map.getSize();
                
                var infoParams = {
                    request: 'GetFeatureInfo',
                    FEATURE_COUNT: '50',
                    query_layers: layer.wmsParams.layers,
                    info_format: 'application/json',
                    X: Math.round(point.x),
                    Y: Math.round(point.y),
                    // bbox: internal._map.getBounds().toBBoxString(),
                    height: size.y,
                    width: size.x,
                    // tilesOrigin // used for tile based WMS
                    // tiled: true //
                };
                console.log(infoParams);
                return L.extend({}, layer.wmsParams, infoParams);
            },

            'parseFeatureInfo': function (result, url) {
                
                return JSON.stringify(result);
            },

            'showFeatureInfo': function (latlng, info) {
                
                if (!internal._map) {
                    return;
                }
                
                internal._L.popup().setLatLng(latlng).setContent(info).openOn(internal._map);
            },
        };


        var module = {

            init: function (L, map, layer) {
                console.info("FeatureInfo.init called", map, layer);
                internal._map = map;
                internal._L = L;
                internal._layer = layer;
                internal._url = layer._url;
                map.on('click', internal.handleMapClick, internal);
            }
        }

        return module;
    });
