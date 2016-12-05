@extends('management-layout')


@section('action-menu')
	
	<div class="action-group">

		<a href="{{route('projects.create')}}" class="button" rv-on-click="createGroup">
			<span class="btn-icon icon-content-white icon-content-white-ic_add_circle_outline_white_24dp"></span>{{trans('projects.new_button')}}
		</a>

	</div>

	<div class="separator"></div>
@stop



@section('content')



<div id="documents-list">

	

	<div class="row">

		<div class="three columns">
			<div class="sidemenu">
				
				
				
				@forelse($projects as $p)
				
					
						<a href="{{route('projects.show', ['id' => $p->id])}}" class="sidemenu__item @if(isset($project) && $project->id == $p->id) current @endif">
							<span class="sidemenu__item__icon icon-action-black icon-action-black-ic_group_work_black_24dp"></span> {{$p->name}}
						</a>
					
					
				@empty
				
					{!!trans('projects.empty_projects', ['url' => route('projects.create')])!!}
				
				@endforelse
				
				
			</div>
		</div>

		<div class="nine columns " id="document-area" data-drop="true">
			
			@if(isset($hint) && $hint)
						
				<div class="alert info">
					{{$hint}}
				</div>
			
			@endif
			
			@include('errors.list')
			
			
			@yield('project_area')
			
		
			
		</div>

	</div>

</div>





@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	// require(['modules/people'], function(People){
	
	// });
	</script>

@stop