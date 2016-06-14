@extends('management-layout')



@section('sub-header')

	

		{{trans('groups.people.page_title')}}


@stop


@section('action-menu')
	
	<div class="action-group">

		<a href="#pub" class="button" rv-on-click="createGroup">
			<span class="btn-icon icon-social-white icon-social-white-ic_group_add_white_24dp"></span>{{trans('actions.create_people_group')}}
		</a>

	</div>

	<div class="separator"></div>
@stop



@section('content')



<div id="groups-list">

	

	<div class="row">

		<div class="three columns">

			<div class="selectable-users">
				
				<h4>{{trans('groups.people.available_users')}}</h4>
				
				<p class="description">{{trans('groups.people.available_users_hint')}}</p>
				
				@forelse($available_users as $user)
				
					<a href="#{{$user->name}}" draggable="true" class="user-grab" data-id="{{$user->id}}">
						<span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>
						{{$user->name}}
					</a>
				
				@empty
				
					<p class="description">{{trans('groups.people.no_users')}}</p>
					
				@endforelse
				
				
			</div>

		</div>

		<div class="nine columns " id="document-area" data-drop="true">
			
			@if(isset($hint) && $hint)
			
				<div class="alert info">
					{{$hint}}
				</div>
			
			@endif

			
			
			<div rv-template="details" class="loadable" >
	
	            {% _.forEach(groups, function(el) { %}
	
				<div class="group-box box {% if(el.saving){ %} saving {% } %}" id="{# el.id #}" data-id="{# el.id #}" data-public="{# el.is_institution_group #}">
					
					{% if(el.saving){ %} <div class="cache">{{trans('actions.saving')}}</div> {% } %}
					
					<div class="u-pull-right">
						
						@if($user_can_institutional && false)
						
							{% if(el.is_institution_group){ %}
						
								<a href="#inst" rv-on-click="makePersonal" class="button">
									<span class="btn-icon icon-action-black icon-action-black-ic_account_circle_black_24dp"></span>{{trans('actions.make_personal_people_group')}}
								</a>
						
							{% }else{ %}
							
								<a href="#inst" rv-on-click="makeInstitutional" class="button">
									<span class="btn-icon icon-action-black icon-action-black-ic_account_balance_black_24dp"></span>{{trans('actions.make_institutional_people_group')}}
								</a>
						
							{% } %}
						
						@endif
						
						<a href="#delete" rv-on-click="deleteGroup" class="button">
							<span class="btn-icon icon-action-black icon-action-black-ic_delete_black_24dp"></span>{{trans('actions.delete_people_group')}}
						</a>
					</div>
					
					
					<h4><a href="{{route('people.index')}}/{# el.id #}">{# el.name #}</a> <span rv-on-click="renameGroup" title="{{trans('actions.rename_people_group')}}" class="btn-icon icon-content-black icon-content-black-ic_create_black_24dp"></span></h4>
	
					<div class="details users-inside">
						{% _.forEach(el.people, function(u) { %}
						
							<div class="user-grab">
								<span class="btn-icon icon-social-black icon-social-black-ic_person_black_24dp"></span>
								{# u.name #} 
								<a href="#delete" data-uid="{# u.id #}" data-uname="{# u.name #}" rv-on-click="removeUserFromGroup" title="{{trans('groups.people.remove_user')}}">
									<span class="btn-icon icon-content-black icon-content-black-ic_clear_black_24dp"></span>
								</a>
							</div>
						
						{% }); %}
					</div>
					
				</div>
	
	            {% }); %}

			</div>
		</div>

	</div>

</div>



@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(['modules/people'], function(People){
		People.data({!! $groups !!}, {!! $available_users_encoded !!});
	});
	</script>

@stop