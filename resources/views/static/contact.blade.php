@extends('global')

@section('breadcrumbs')

	{{trans('pages.contact')}}

@stop


@section('content')

	<div class="c-page contact__container">

        @if($is_configured)
            @if(!empty($contact['image']))
                <div class="contact__image" style="background-image:url('{{$contact['image']}}')"></div>
            @endif

            <div class="contact__card" itemscope itemtype="http://schema.org/Organization">


            <h3 itemprop="name">{{$contact['name']}}</h3>

            <div class="address" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                
                <span itemprop="streetAddress">{{$contact['address_street']}}</span>
                <span itemprop="postalCode">{{$contact['address_zip']}}</span>
                <span itemprop="addressLocality">{{$contact['address_locality']}}</span>
                <span itemprop="addressCountry">{{$contact['address_country']}}</span>
                
            </div>

            @if(!empty($contact['phone']))
            <div class="phone">
                <span class="icon-action-black icon-action-black-ic_settings_phone_black_24dp"></span> <span itemprop="telephone">{{$contact['phone']}}</span>
            </div>
            @endif

            @if(!empty($contact['email']))
            <div class="mail">
                <span class="icon-content-black icon-content-black-ic_mail_black_24dp"></span> <span itemprop="email">{{$contact['email']}}</span>
            </div>
            @endif

            @if(!empty($contact['website']))
            <div class="website">
                <span class="icon-action-black icon-action-black-ic_language_black_24dp"></span> <a href="{{$contact['website']}}">{{$contact['website']}}</a>
            </div>
            @endif

            </div>

        @endif

  </div>

@stop
