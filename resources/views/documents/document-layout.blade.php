@extends('layout.explore')




@section('action-menu')

	@include('actions.selection')

	@yield('page-action')

		<div class="action__separator"></div>

		@if($context!=='trash' && $context!=='shared' && $context!=='starred' && (
			isset($can_create_collection) && $can_create_collection ||
			isset($can_upload) && $can_upload
		))

			<div class="button  dropdown">
		
				<span class="label">@materialicon('content', 'add_circle_outline', 'inline-block')<span class="hidden md:inline ml-1">{{ trans('actions.create_add_dropdown')}}</span></span>
				<span class="btn-icon expand icon-navigation-white icon-navigation-white-ic_expand_more_white_24dp"></span>
				<span class="btn-icon collapse icon-navigation-white icon-navigation-white-ic_expand_less_white_24dp"></span>

				<ul class="drop-menu">
					
					@if(isset($can_create_collection) && $can_create_collection)
					<li><a href="#" class="dropdown-el"  rv-on-click="createGroup">

					@materialicon('action', 'label', 'inline-block mr-2'){{trans('actions.create_collection_btn')}}

					</a></li>
					
					@endif


					@if($context!=='groups-manage')

						@if(isset($can_create_collection) && $can_create_collection)
							<li class="dropdown-separator"></li>
						@endif

						@if(isset($can_upload) && $can_upload)

							<li><a href="#{{route('documents.create')}}" class="dropdown-el" id="upload_trigger">
								@materialicon('action', 'description', 'inline-block mr-2'){{trans('actions.upload')}}
							</a></li>
							<li><a href="{{ isset($context_group) ? route('uploads.index').'?c='.e($context_group) : route('uploads.index')}}" class="dropdown-el">
								@materialicon('av', 'videocam', 'inline-block mr-2'){{ trans('actions.upload_video') }}
							</a></li>

						@endif

					@endif


				</ul>

			</div>
			

		@endif


		@if(isset($is_klink_public_enabled) && $is_klink_public_enabled && $context!=='trash' && $context!=='shared' && $context!=='public' && isset($can_make_public) && $can_make_public)
			<button class="button ml-2 " rv-on-click="makePublic" rv-disabled="nothingIsSelected" title="{{trans('networks.publish_to_hint', ['network' => network_name()])}}" >
				@materialicon('social', 'public', 'inline-block')<span class="hidden md:inline ml-1">{{trans('networks.publish_to_short')}}</span>
			</button>
		@endif

		@if($context!=='trash' && $context!=='shared' && $context!=='public' && isset($can_share) && $can_share)
			<button class="button ml-2" rv-on-click="share" rv-disabled="nothingIsSelected">
				@materialicon('action', 'launch', 'inline-block')<span class="hidden md:inline ml-1">{{trans('share.share_btn')}}</span>
			</button>
		@endif

		@if($context!=='trash' && $context!=='shared' && $context!=='public' && $context!=='starred' && isset($can_delete_documents) && $can_delete_documents)

			<button class="button ml-2" rv-on-click="del"  rv-disabled="nothingIsSelected">
				@materialicon('action', 'delete', 'inline-block')<span class="hidden md:inline ml-1">{{trans('actions.trash_btn')}}</span>
			</button>

		@endif

		<div class="action__separator"></div>

		@include('actions.list-switcher')

@stop






@section('scripts')

	<script>

	require(['modules/list-switcher', 'modules/documents', 'modules/panels', 'modules/dropdown'], function(Switcher, Documents, Panels, Dropdown){

		Dropdown.find(".js-header");

		@if(isset($context))
			Documents.setContext({
				filter:'{{$context}}',
				maxUploadSize: {{ ceil(\KBox\Upload::maximumAsKB()) }},
				network_name: '{{ network_name() }}',
				@if(isset($context_group)) group: '{{$context_group}}', @endif
				@if(isset($current_visibility)) visibility: '{{$current_visibility}}', @endif
				search: @if(isset($search_terms)) '{{$search_terms}}' @else '' @endif,
				@if(isset($facets)) facets: {!!json_encode($facets)!!}, @endif
				@if(isset($filters)) filters: {!!json_encode($filters)!!}, @endif
				isSearchRequest: {{ isset($is_search_requested) && $is_search_requested ? 'true' : 'false' }},
				canPublish: {{ isset($can_make_public) && isset($is_klink_public_enabled) && $is_klink_public_enabled && $can_make_public ? 'true' : 'false' }},
				canShare: {{ isset($can_share) && $can_share ? 'true' : 'false' }},
				userIsProjectManager: {{ auth()->check() && auth()->user()->isProjectManager() ? 'true' : 'false' }}
			});
			Documents.groups.ensureCurrentVisibility();
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop