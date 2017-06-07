@extends('management-layout')



@section('sub-header')

	@if(isset($filter) && !is_null($filter) )
		
		@if($context ==='group' && isset($context_group_instance))
		
			@if($context_group_instance->is_private)
				
				<span class="parent">{{trans('groups.collections.personal_title')}}</span>
			
			@else 
				
				<a href="{{route('documents.projects.index')}}" class="parent">{{trans('groups.collections.private_title')}}</a>
			
			@endif

		@elseif($context!=='projectspage')
		
			<a href="{{route('documents.index')}}" class="parent">{{trans('documents.page_title')}}</a>
		
		@endif
		
		 

		@if(isset($parents) && $context ==='group')

			@foreach ($parents as $parent)

				<a href="{{route('documents.groups.show', $parent->id)}}" class="parent">{{$parent->name}}</a>
			
			@endforeach

		@endif


		{{$filter}}

	@else

		{{trans('documents.page_title')}}

	@endif



@stop


@section('action-menu')

	@include('actions.order-group')

	<div class="separator"></div>

		@yield('page-action')

		<div class="separator"></div>

		@if($context!=='trash' && $context!=='shared' && $context!=='starred' && (
			isset($can_create_collection) && $can_create_collection ||
			isset($can_upload) && $can_upload ||
			isset($can_import) && $can_import
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

						@if(isset($can_import) && $can_import)

							<li><a href="{{route('import')}}" class="dropdown-el">
								{{trans('actions.import')}}
							</a></li>

						@endif

					@endif


				</ul>

			</div>
			
			

		</div>

		@endif

		<div class="action-group">

			@if(isset($is_klink_public_enabled) && $is_klink_public_enabled && $context!=='trash' && $context!=='shared' && $context!=='public' && isset($can_make_public) && $can_make_public)
				<a href="#pub" class="button hint--bottom" rv-on-click="makePublic" data-hint="{{trans('networks.publish_to_hint', ['network' => network_name()])}}" >
					<span class="btn-icon icon-social-white icon-social-white-ic_public_white_24dp"></span>{{trans('networks.publish_to_short')}}
				</a>
			@endif

			@if($context!=='trash' && $context!=='shared' && isset($can_share) && $can_share)
				<a href="#share" class="button" rv-on-click="share" rv-disabled="nothingIsSelected">
					<span class="btn-icon icon-action-white icon-action-white-ic_launch_white_24dp"></span>{{trans('share.share_btn')}}
				</a>
			@endif

			
			

			<!--
			@if($context!=='trash')

				<a href="#" class="button disabled" rv-class-disabled="somethingIsSelected | invert" rv-disabled="somethingIsSelected | invert">
					<span class="btn-icon icon-file-white icon-file-white-ic_folder_white_24dp"></span>
					{{trans('actions.add_or_move_to')}}
				</a>

			@endif
			-->

			@if($context!=='trash' && $context!=='shared' && $context!=='starred' && isset($can_delete_documents) && $can_delete_documents)

				<a href="#" class="button" rv-on-click="del"  rv-disabled="nothingIsSelected">
					<span class="btn-icon icon-action-white icon-action-white-ic_delete_white_24dp"></span>{{trans('actions.trash_btn')}}
				</a>

			@endif

		</div>

		<div class="separator"></div>

		

		

		

		@include('actions.list-switcher')

	

	

@stop

@section('document_list_area')

<div class="drag-message">
	{{trans('documents.messages.drag_hint')}}
</div>



	<div class="list {{$list_style_current}}" >

		@yield('document_area')

	</div>
	
	@if( isset($pagination) && !is_null($pagination) )
		<div class="pagination-container">

			{!! $pagination->render() !!}

		</div>
	@endif

@stop

@section('content')

@include('map.map')

<div id="documents-list" class="non-map">

	

	<div class="row">

		<div class="three columns">

			@include('documents.menu')

		</div>

		<div class="nine columns " id="document-area">
			
			@if(isset($hint) && $hint)
			
				<div class="alert info">
					{{$hint}}
				</div>
			
			@endif
			
			@if(isset($context) && $context!=='recent')
				@include('documents.facets')
			@endif

			@yield('document_list_area')

		</div>

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

		var subHeader = $(".sub-header");

		if(subHeader){
			
			var actions = subHeader.find(".actions");
			var parentNavigation = subHeader.find(".parent-navigation");

			parentNavigation.width(subHeader.width() - actions.width() - 50 /*scrollbar width*/);
		}

	})();

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
				canPublish: {{ isset($can_make_public) && isset($is_klink_public_enabled) && $is_klink_public_enabled && $can_make_public ? 'true' : 'false' }}
			});
			Documents.groups.ensureCurrentVisibility();
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop