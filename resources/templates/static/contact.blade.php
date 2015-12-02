@extends('static.static-layout')

@section('sub-header')

	{{trans('pages.contact')}}

@stop


@section('content')

	<div class="big-map" id="bigmap">

    

      <div class="contact-card" itemscope itemtype="http://schema.org/{{$inst->type}}">

        <div class="institution-image">

            <img src="{{$inst->thumbnail_uri}}">

        </div>

        <h3 itemprop="name">{{$inst->name}}</h3>


        <div class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
          
            <span itemprop="streetAddress">{{$inst->address_street}}</span>
            <span itemprop="postalCode">{{$inst->address_zip}}</span>
            <span itemprop="addressLocality">{{$inst->address_locality}}</span>
            <span itemprop="addressCountry">{{$inst->address_country}}</span>
          
        </div>

        <div class="phone">
          <span class="icon-action-black icon-action-black-ic_settings_phone_black_24dp"></span> <span itemprop="telephone">{{$inst->phone}}</span>
        </div>
        <div class="mail">
          <span class="icon-content-black icon-content-black-ic_mail_black_24dp"></span> <span itemprop="email">{{$inst->email}}</span>
        </div>

        <div class="website">
          <span class="icon-action-black icon-action-black-ic_language_black_24dp"></span> <a href="{{$inst->url}}">{{$inst->url}}</a>
        </div>

        


        <p>{{trans('units.klink_since', ['date' => $since])}}</p>

      </div>

      

    

  </div>


@stop

@section('scripts')

  <script>
            
  require(['leaflet'], function(Leaflet){

    var map = Leaflet.noConflict().map('bigmap', {
        center: [{{$geocode['lat']}},{{$geocode['lon']}}],
        zoom: 10,
        touchZoom:false,
        scrollWheelZoom:false,
        doubleClickZoom:false,
        boxZoom:false,
        zoomControl:false
    });

    Leaflet.tileLayer('//{s}.tiles.mapbox.com/v3/examples.map-i875mjb7/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.mapbox.com">MapBox</a>'
    }).addTo(map);

    

  });
  
  </script>

@stop