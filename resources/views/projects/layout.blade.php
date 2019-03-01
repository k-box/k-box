@extends('global')


@section('action-menu')

	<a href="{{route('projects.create')}}" class="action__button" rv-on-click="createGroup">
		@materialicon('content', 'add_circle_outline'){{trans('projects.new_button')}}
	</a>
@stop



@section('content')



<div id="documents-list">

	
	<div class="sidebar">
		<div class="navigation navigation--secondary">
			
			@forelse($projects as $p)
			
				
				<a href="{{route('projects.show', ['id' => $p->id])}}" class="navigation__item navigation__item--link @if(isset($project) && $project->id == $p->id) navigation__item--current @endif">
					{{$p->name}}
				</a>
				
				
			@empty
			
				{!!trans('projects.empty_projects', ['url' => route('projects.create')])!!}
			
			@endforelse
			
			
		</div>
	</div>

	<div class="sidebar__rest" id="document-area" data-drop="true">
		
		@if(isset($hint) && $hint)
					
			<div class="alert info">
				{{$hint}}
			</div>
		
		@endif
		
		@include('errors.list')
		
		
		@yield('project_area')
		
	
		
	</div>

</div>





@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>

	</script>

@stop