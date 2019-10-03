@if(!is_null($item->collection))
<div class="item item--project document-item" data-id="{{$item->id}}" data-project="{{$item->id}}"  rv-on-click="openProject" data-class="project" data-group-id="{{$item->collection->id}}" data-drop="true">
	
	<div class="item__title list__column list__column--large">

		<div class="item__icon">
			@materialicon('action', 'label')
		</div>

		<div class="item__thumbnail klink-project">
			
			<img src="{{asset('images/transparent.png')}}" @if($item->avatar) data-src="{{ route('projects.avatar.index', ['id' => $item->id]) }}" @endif />

		</div>

		<a href="{{route('documents.groups.show', $item->collection->id)}}" class="item__link" title="{{ $item->name }}">
			{{ $item->name }}
		</a>
		
	</div>

	<span class="item__detail list__column list__column--hideable" title="@datetime($item->created_at)">
		@date($item->created_at)
	</span>
	
	<span class="item__detail list__column" title="{{trans('projects.labels.managed_by')}}">
		{{$item->manager->name}}
	</span>
	
	<span class="item__detail list__column list__column--hideable" title="{{trans('projects.labels.documents_count_label')}}">
		{{ trans_choice('projects.labels.documents_count', $item->getDocumentsCount(), ['count' => $item->getDocumentsCount()]) }}
	</span>
	
	<span class="item__detail list__column list__column--hideable" title="{{trans('projects.labels.user_count_label')}}">
		{{ trans_choice('projects.labels.user_count', $item->users->count(), ['count' => $item->users->count()]) }}
	</span>
	

</div>
@endif
