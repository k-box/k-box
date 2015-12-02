@extends('widgets.widget-layout')


@section('widget_title')
<span class="widget-icon icon-action-black icon-action-black-ic_account_circle_black_24dp"></span> {{trans('widgets.user_activity.title')}}
@overwrite

@section('widget_content')
{{trans('widgets.user_activity.empty')}}
@overwrite