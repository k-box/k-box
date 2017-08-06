@extends('widgets.widget-layout')


@section('widget_title')
<span class="widget-icon icon-action-black icon-action-black-ic_settings_black_24dp"></span> {{trans('widgets.dms_admin.title')}}
@overwrite

@section('widget_content')

	@include('administration.adminmenu', ['small' => true])

@overwrite