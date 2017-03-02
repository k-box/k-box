
<div class="c-widget widget--storage">
	
	<h5 class="widget__title">
		{{trans('widgets.storage.title')}}
	</h5>

	@if(isset($storage_status))

		<span>{{ trans('widgets.storage.used', ['used' => $storage_status['used'], 'total' => $storage_status['total']]) }}</span>

		<div class="widget--storage__meter">
			<div class="bar" style="width:{{$storage_status['percentage'] }}%"></div>
		</div>

	@endif

</div>
