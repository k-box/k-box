@extends('layout.sidebar')



@section('breadcrumbs')

	@if(isset($filter) && !is_null($filter) )
		
		@if($context ==='group' && isset($context_group_instance))
		
			@if($context_group_instance->is_private)
				
				<span  class="breadcrumb__item">{{trans('groups.collections.personal_title')}}</span>
			
			@else 
				
				<a href="{{route('documents.projects.index')}}"  class="breadcrumb__item">{{trans('groups.collections.private_title')}}</a>
			
			@endif

		@elseif($context!=='projectspage')
		
			<a href="{{route('documents.index')}}"  class="breadcrumb__item">{{trans('documents.page_title')}}</a>
		
		@endif
		
		 

		@if(isset($parents) && $context ==='group')

			@foreach ($parents as $parent)

				<a href="{{route('documents.groups.show', $parent->id)}}"  class="breadcrumb__item">{{$parent->name}}</a>
			
			@endforeach

		@endif


		<span class="breadcrumb__item--current">{{$filter}}</span>

	@else

		<span class="breadcrumb__item--current">{{trans('documents.page_title')}}</span>

	@endif



@stop


@section('action-menu')

	@if(isset($documents))
		@include('actions.selection')


		@if($context!=='trash' && $context!=='shared' && $context!=='starred' &&
			!$is_search_requested && (
			isset($can_create_collection) && $can_create_collection ||
			isset($can_upload) && $can_upload
		))

		<div class="action-group">

			<div class="button dropdown">
		
				<span class="label"><span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('actions.create_add_dropdown')}}</span>
				<span class="btn-icon expand icon-navigation-white icon-navigation-white-ic_expand_more_white_24dp"></span>
				<span class="btn-icon collapse icon-navigation-white icon-navigation-white-ic_expand_less_white_24dp"></span>

				<ul class="drop-menu">
					
					@if(isset($can_create_collection) && $can_create_collection)
					<li><a href="#" class="dropdown-el"  rv-on-click="createGroup">

					<span class="btn-icon icon-action-white icon-action-white-ic_label_white_24dp"></span>{{trans('actions.create_collection_btn')}}

					</a></li>
					
					@endif


					@if($context!=='groups-manage')

						@if(isset($can_create_collection) && $can_create_collection)
							<li class="separator"></li>
						@endif

						@if(isset($can_upload) && $can_upload)

							<li><a href="#{{route('documents.create')}}" class="dropdown-el" id="upload_trigger">
								<span class="btn-icon icon-file-white icon-file-white-ic_file_upload_white_24dp"></span>{{trans('actions.upload')}}
							</a></li>

						@endif

					@endif


				</ul>

			</div>
			
			

		</div>

		@endif

		@if(isset($is_klink_public_enabled) && $is_klink_public_enabled && $context!=='trash' && $context!=='shared' && $context!=='public' && isset($can_make_public) && $can_make_public)
			<a href="#pub" class="action__button hint--bottom" rv-on-click="makePublic" data-hint="{{trans('networks.publish_to_hint', ['network' => network_name()])}}" >
				@materialicon('social', 'public'){{trans('networks.publish_to_short')}}
			</a>
		@endif

		@if($context!=='trash' && $context!=='shared' && isset($can_share) && $can_share)
			<a href="#share" class="action__button" rv-on-click="share" rv-disabled="nothingIsSelected">
				@materialicon('action', 'launch'){{trans('share.share_btn')}}
			</a>
		@endif

		
		

		@if($context!=='trash' && $context!=='shared' && $context!=='starred' && isset($can_delete_documents) && $can_delete_documents)

			<a href="#" class="action__button" rv-on-click="del"  rv-disabled="nothingIsSelected">
				@materialicon('action', 'delete'){{trans('actions.trash_btn')}}
			</a>

		@endif

	@elseif(auth()->check() && auth()->user()->can_capability(\KBox\Capability::CREATE_PROJECTS))

		<a href="{{route('projects.create')}}" class="action__button">
			@materialicon('content', 'add_circle_outline'){{trans('projects.new_button')}}
		</a>

	@endif
		


	@include('actions.list-switcher')

	

	

@stop

@section('document_list_area')


@if(isset($info_message) && !is_null($info_message))
<div class="c-message c-message--info">
	{{$info_message}}
</div>
@endif

	<div class="list {{$list_style_current}}" >

		<div class="list__header">
			@section('list_header')

				<div class="list__column list__column--large">{{trans('documents.descriptor.name')}}</div>

				@if(!$is_search_requested)
					<div class="list__column list__column--hideable">{{trans('projects.labels.created_on')}}</div>
					<div class="list__column">{{trans('projects.labels.managed_by')}}</div>
					<div class="list__column list__column--hideable">{{trans('projects.labels.documents_count_label')}}</div>
					<div class="list__column list__column--hideable">{{trans('projects.labels.user_count_label')}}</div>
				@else 
					<div class="list__column list__column--hideable">{{trans('documents.descriptor.added_by')}}</div>
					<div class="list__column">{{trans('documents.descriptor.last_modified')}}</div>
				<div class="list__column list__column--hideable">{{trans('documents.descriptor.language')}}</div>
				@endif
			@endsection

			@yield('list_header')
		</div>

		@yield('document_area')

	</div>
	
	@if( isset($pagination) && !is_null($pagination) )
		<div class="pagination-container">

			{!! $pagination->render() !!}

		</div>
	@endif

@stop

@section('sidebar')

@include('documents.menu')
	
@endsection

@section('page')


<div id="documents-list" class="non-map">

	<div id="document-area">
		
		@if(isset($hint) && $hint)
		
			<div class="alert info">
				{{$hint}}
			</div>
		
		@endif

		@include('errors.list')
		
		
		@include('documents.facets')
		

		@yield('document_list_area')

	</div>

</div>

@include('documents.partials.uploadinfo')

@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>

	(function(){

		var subHeader = $(".breadcrumbs");

		if(subHeader){
			
			var actions = subHeader.find(".actions");
			var parentNavigation = subHeader.find(".parent-navigation");

			parentNavigation.width(subHeader.width() - actions.width() - 50 /*scrollbar width*/);
		}

	})();

	require(['modules/documents', 'modules/list-switcher'], function(Documents){

		@if(isset($context))
			Documents.setContext({
				filter:'{{$context}}',
				network_name: '{{ network_name() }}',
				@if(isset($context_group)) group: '{{$context_group}}', @endif
				@if(isset($current_visibility)) visibility: '{{$current_visibility}}', @endif
				search: @if(isset($search_terms)) '{{$search_terms}}' @else '' @endif,
				@if(isset($facets)) facets: {!!json_encode($facets)!!}, @endif
				@if(isset($filters)) filters: {!!json_encode($filters)!!}, @endif
				isSearchRequest: {{ isset($is_search_requested) && $is_search_requested ? 'true' : 'false' }},
				canShare: {{ isset($can_share) && $can_share ? 'true' : 'false' }},
				canPublish: {{ isset($can_make_public) && isset($is_klink_public_enabled) && $is_klink_public_enabled && $can_make_public ? 'true' : 'false' }},
				userIsProjectManager: {{ auth()->check() && auth()->user()->isProjectManager() ? 'true' : 'false' }}
			});
			Documents.groups.ensureCurrentVisibility();
		@endif

		@if(isset($can_upload) && $can_upload)
			Documents.initUploadService();
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop