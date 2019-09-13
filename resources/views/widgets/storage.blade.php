
<div class="c-widget widget--storage">
	
	<h4 class="widget__title">
		{{trans('widgets.storage.title')}}
	</h4>

	@if(isset($storage_status))

		@component('components.progress', ['percentage' => $storage_status['percentage']])
			{{ trans('widgets.storage.used', ['used' => $storage_status['used'], 'total' => $storage_status['total']]) }}
		@endcomponent

	@endif

</div>
