@extends('global')


@section('breadcrumbs')

	<a href="{{route('people.index')}}"  class="breadcrumb__item">{{trans('groups.people.page_title')}}</a>

	{{$pagetitle}}


@stop

@section('page-action')



@stop

@section('content')

	<h4>Documents shared with {{$pagetitle}}</h4>
	
	<div>
	@forelse($people as $user)
				
		<a href="#{{$user->name}}" class="user-grab" data-id="{{$user->id}}">
			<span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>
			{{$user->name}}
		</a>
	
	@empty
	
		No users in this group
		
	@endforelse
	
	</div>
	
	@if(!is_null($shares))

	<div id="document-area" class="share-section shared-with-me clearfix">


		<div class="list tiles" >

			@forelse ($shares as $result)
		
				@include('documents.descriptor', ['item' => $result->shareable])
		
			@empty
		
				<p>{{ trans('share.empty_with_me_message') }}</p>
		
			@endforelse

		</div>

	</div>
	
	@endif


@stop


@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/documents'], function(Documents){

		@if(isset($context))
			Documents.setContext({
				filter:'{{$context}}',
				@if(isset($context_group)) group: '{{$context_group}}', @endif
				@if(isset($current_visibility)) visibility: '{{$current_visibility}}', @endif
				search: @if(isset($search_terms)) '{{$search_terms}}' @else '' @endif,
				@if(isset($facets)) facets: {!!json_encode($facets)!!}, @endif
				@if(isset($filters)) filters: {!!json_encode($filters)!!} @endif
			});
			
		@endif

		@yield('document_script_initialization')
		
	});
	</script>

@stop