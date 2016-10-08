@extends('management-layout')

@section('sub-header')

    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> <a href="{{route('administration.institutions.index')}}" class="parent">{{trans('administration.menu.institutions')}}</a> {{$institution->name}}

@stop

@section('content')

    <h3>{{ $institution->name }}</h3>


    <div class="row">

    <div class="six columns">

    

    <p>
        
        <label>{{trans('administration.institutions.labels.klink_id')}}</label>
        
        <strong>{{ $institution->klink_id }}</strong>
        
    </p>

    <p>
        
        <label>{{trans('administration.institutions.labels.name')}}</label>
        
        {{ $institution->name }}
    </p>
    
    <p>
        
        <label>{{trans('administration.institutions.labels.email')}}</label>
        
        {{ $institution->email }}
    </p>
    
    <p>
        
        <label>{{trans('administration.institutions.labels.phone')}}</label>
        
        {{ $institution->phone }}
    </p>
    
    <p>
        
        <label>{{trans('administration.institutions.labels.url')}}</label>

        <a href="{{ $institution->url }}">{{ $institution->url }}</a>
    </p>

     <p>
        
        <label>{{trans('administration.institutions.labels.address_street')}}</label>

        {{ $institution->address_street }}
    </p>
    
    <p>
        
        <label>{{trans('administration.institutions.labels.address_locality')}}</label>
        
        {{ $institution->address_locality }}
    </p>
    
    <p>
        
        <label>{{trans('administration.institutions.labels.address_country')}}</label>
        
        {{ $institution->address_country }}
    </p>
    
    <p>
        
        <label>{{trans('administration.institutions.labels.address_zip')}}</label>
        
        {{ $institution->address_zip }}
    </p>
    
    </div>
    
    <div class="six columns">
    
    @if($institution->thumbnail_uri)

    <img src="{{$institution->thumbnail_uri}}" style="max-width:100%">

    @endif
    
   

    </div>
    
    </div>

    
@stop