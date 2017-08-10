@extends('global')



@section('breadcrumbs')

	@if(isset($filter) && !is_null($filter) )
		
		@if($context ==='group' && isset($context_group_instance))
		
			@if($context_group_instance->is_private)
				
				<span class="breadcrumb__item">{{trans('groups.collections.personal_title')}}</span>
			
			@else 
				
				<a href="{{route('documents.projects.index')}}" class="breadcrumb__item">{{trans('groups.collections.private_title')}}</a>
			
			@endif

		@elseif($context!=='projectspage')
		
			<a href="{{route('documents.index')}}" class="breadcrumb__item">{{trans('documents.page_title')}}</a>
		
		@endif
		
		 

		@if(isset($parents) && $context ==='group')

			@foreach ($parents as $parent)

				<a href="{{route('documents.groups.show', $parent->id)}}" class="breadcrumb__item">{{$parent->name}}</a>
			
			@endforeach

		@endif


		<span class="breadcrumb__item--current">{{$filter}}</span>

	@else

		<span class="breadcrumb__item--current">{{trans('documents.page_title')}}</span>

	@endif



@stop


@section('action-menu')

	@include('actions.order-group')

	

		@yield('page-action')

		<div class="action__separator"></div>

		@if($context!=='trash' && $context!=='shared' && $context!=='starred' && (
			isset($can_create_collection) && $can_create_collection ||
			isset($can_upload) && $can_upload ||
			isset($can_import) && $can_import
		))

		

			<div class="action__button dropdown">
		
				<span class="label">@materialicon('content', 'add_circle_outline'){{trans('actions.create_add_dropdown')}}</span>
				<span class="btn-icon expand icon-navigation-white icon-navigation-white-ic_expand_more_white_24dp"></span>
				<span class="btn-icon collapse icon-navigation-white icon-navigation-white-ic_expand_less_white_24dp"></span>

				<ul class="drop-menu">
					
					@if(isset($can_create_collection) && $can_create_collection)
					<li><a href="#" class="dropdown-el"  rv-on-click="createGroup">

					@materialicon('action', 'label'){{trans('actions.create_collection_btn')}}

					</a></li>
					
					@endif


					@if($context!=='groups-manage')

						@if(isset($can_create_collection) && $can_create_collection)
							<li class="dropdown-separator"></li>
						@endif

						@if(isset($can_upload) && $can_upload)

							<li><a href="#{{route('documents.create')}}" class="dropdown-el" id="upload_trigger">
								@materialicon('action', 'description'){{trans('actions.upload')}}
							</a></li>
							<li><a href="{{route('uploads.index')}}" class="dropdown-el">
								@materialicon('file', 'file_upload')New Uploader (preview)
							</a></li>

						@endif

						@if(isset($can_import) && $can_import)

							<li><a href="{{route('documents.import')}}" class="dropdown-el">
								{{trans('actions.import')}}
							</a></li>

						@endif

					@endif


				</ul>

			</div>
			

		@endif


		@if(isset($is_klink_public_enabled) && $is_klink_public_enabled && $context!=='trash' && $context!=='shared' && $context!=='public' && isset($can_make_public) && $can_make_public)
			<button class="action__button hint--bottom" rv-on-click="makePublic" rv-disabled="nothingIsSelected" data-hint="{{trans('networks.publish_to_hint', ['network' => network_name()])}}" >
				@materialicon('social', 'public'){{trans('networks.publish_to_short')}}
			</button>
		@endif

		@if($context!=='trash' && $context!=='shared' && isset($can_share) && $can_share)
			<button class="action__button" rv-on-click="share" rv-disabled="nothingIsSelected">
				@materialicon('action', 'launch'){{trans('share.share_btn')}}
			</button>
		@endif

		@if($context!=='trash' && $context!=='shared' && $context!=='starred' && isset($can_delete_documents) && $can_delete_documents)

			<button class="action__button" rv-on-click="del"  rv-disabled="nothingIsSelected">
				@materialicon('action', 'delete'){{trans('actions.trash_btn')}}
			</button>

		@endif

		<div class="action__separator"></div>

		@include('actions.list-switcher')

@stop

@section('document_list_area')

	<div class="list {{$list_style_current}}" >

		<div class="list__header">
			@section('list_header')
				<div class="list__column list__column--large">{{trans('documents.descriptor.name')}}</div>
				<div class="list__column list__column--hideable">{{trans('documents.descriptor.added_by')}}</div>
				{{-- <div class="list__column">{{trans('share.shared_by_label')}}</div>
				<div class="list__column">{{trans('share.shared_on')}}</div> --}}
				<div class="list__column">{{trans('documents.descriptor.last_modified')}}</div>
				<div class="list__column list__column--hideable">{{trans('documents.descriptor.language')}}</div>
				{{-- <div class="list__column">File size</div> --}}
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

@section('content')

<div id="documents-list">

	<div class="sidebar js-drawer">

		@include('documents.menu')

	</div>

	<div class="sidebar__rest" id="document-area">
		
		@if(isset($hint) && $hint)
		
			<div class="alert info">
				{{$hint}}
			</div>
		
		@endif

		@include('errors.list')
		
		@if(isset($context) && ($context!=='recent' && $context!=='uploads'))
			@include('documents.facets')
		@endif

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

	require(['modules/list-switcher', 'modules/documents', 'modules/panels'], function(Switcher, Documents, Panels){

		@if(isset($context))
			Documents.setContext({
				filter:'{{$context}}',
				maxUploadSize: {{ ceil(\Config::get('dms.max_upload_size')/1024) }},
				network_name: '{{ network_name() }}',
				@if(isset($context_group)) group: '{{$context_group}}', @endif
				@if(isset($current_visibility)) visibility: '{{$current_visibility}}', @endif
				search: @if(isset($search_terms)) '{{$search_terms}}' @else '' @endif,
				@if(isset($facets)) facets: {!!json_encode($facets)!!}, @endif
				@if(isset($filters)) filters: {!!json_encode($filters)!!}, @endif
				isSearchRequest: {{ isset($is_search_requested) && $is_search_requested ? 'true' : 'false' }},
				canPublish: {{ isset($can_make_public) && isset($is_klink_public_enabled) && $is_klink_public_enabled && $can_make_public ? 'true' : 'false' }},
				userIsProjectManager: {{ auth()->check() && auth()->user()->isProjectManager() ? 'true' : 'false' }}
			});
			Documents.groups.ensureCurrentVisibility();
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop