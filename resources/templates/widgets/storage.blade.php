@extends('widgets.widget-layout')


<!-- Expecting storage_status variable -->

@section('widget_class')
storage-statistics
@overwrite

@section('widget_title')
<span class="widget-icon icon-action-black icon-action-black-ic_dns_black_24dp"></span> {{trans('widgets.storage.title')}}
@overwrite

@section('widget_content')

<div class="document">

@foreach($storage_status['document_categories'] as $key => $values)

	@if($values['total'] > 0)
		<p><strong>{{$values['total']}}</strong> {{trans_choice('documents.type.' . $key, $values['total'])}}</p>
	@endif

@endforeach

</div>

<div class="storage">

	<h4>{{$storage_status['full_percentage']}}%</h4>

	<span>{!!trans('widgets.storage.free', ['free' => $storage_status['free_space_on_docs_folder'], 'total' => $storage_status['total_space_on_docs_folder']])!!}</span>

</div>





@overwrite