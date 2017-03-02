@if(!is_null($item->collection))
<div class="item item--project" data-id="{{$item->id}}"  rv-on-click="openProject" data-class="project" data-group-id="{{$item->collection->id}}" data-drop="true">

	<span class="selection-area"></span>
	
    <div class="icon" >
		<span class="klink-document-icon klink-project"></span>
	</div>

	<div class="thumbnail klink-project">
		
		<img src="{{asset('images/transparent.png')}}" @if($item->avatar) data-src="{{ route('projects.avatar.index', ['id' => $item->id]) }}" @endif />

	</div>

	<h2 class="title">

		<a href="{{route('documents.groups.show', $item->collection->id)}}" class="link" title="{{ $item->name }}">
			{{ $item->name }}
		</a>
		
	</h2>

	<div class="meta">
		<span class="meta-info" title="{{trans('projects.labels.created_on')}} {{$item->getCreatedAt(true)}}">
			<span class="meta-label">{{trans('projects.labels.created_on')}}&nbsp;</span>{{$item->getCreatedAt()}}
		</span>
		
		<span class="meta-info" title="{{trans('projects.labels.managed_by')}}">
            <span class="meta-label">{{trans('projects.labels.managed_by')}}&nbsp;</span> {{$item->manager->name}}
		</span>
		
		<span class="meta-info" title="{{trans('projects.labels.documents_count_label')}}">
			<span class="meta-label">{{trans('projects.labels.documents_count_label')}}&nbsp;</span>{{ trans_choice('projects.labels.documents_count', $item->getDocumentsCount(), ['count' => $item->getDocumentsCount()]) }}
		</span>
		
		<span class="meta-info" title="{{trans('projects.labels.user_count_label')}}">
			<span class="meta-label">{{trans('projects.labels.user_count_label')}}&nbsp;</span>{{ trans_choice('projects.labels.user_count', $item->users->count(), ['count' => $item->users->count()]) }}
		</span>
	</div>

</div>
@endif
