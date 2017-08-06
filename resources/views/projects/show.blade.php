@extends('projects.layout')



@section('breadcrumbs')

	<a href="{{route('projects.index')}}" class="breadcrumb__item">{{trans('projects.page_title')}}</a> {{$project->name}}

@stop


@section('action-menu')

    <a href="{{route('projects.edit', ['id' => $project->id])}}" class="action__button">
        @materialicon('content', 'create', 'button__icon'){{trans('projects.edit_button')}}
    </a>

    <a href="{{route('projects.create')}}" class="action__button">
        @materialicon('content', 'add_circle_outline', 'button__icon'){{trans('projects.new_button')}}
    </a>

@stop



@section('project_area')

	<h3>{{$project->name}}</h3>
	
	<p>{{$project->description}}</p>


	<div class="row">
		
		<div class="six columns">

            <a class="u-pull-right" href="{{route('projects.edit', ['id' => $project->id])}}#add-members">{{ trans('projects.labels.add_users_button') }}</a>

            <h4>{{trans('projects.labels.users')}}</h4>

            

            @include('projects.partials.userlist', ['users' => $project_users, 'description' => null, 'empty_message' => trans('projects.no_members'), 'edit' => false ])

		</div>
		
        <div class="six columns">
			<h4>{!! trans('microsites.labels.microsite') !!}</h4>
			<p>
                <span class="description">{{trans('microsites.hints.what')}}</span>
            </p>
            
            @if( is_null( $project->microsite ) )
            
                <p>
                    <a id="microsite_create" href="{{ route('microsites.create', ['project' => $project->id]) }}" class="button">{{ trans('microsites.actions.create') }}</a>
                    <span class="description">{{ trans('microsites.hints.create_for_project') }}</span>
                </p>
            
            @else 
            
                <p>
                    <a target="_blank" href="{{ route('projects.site', ['slug' => $project->microsite->slug]) }}" class="button">{{ trans('microsites.actions.view_site') }}</a>
                </p>
            
                <p>
                    <a id="microsite_edit" href="{{ route('microsites.edit', ['id' => $project->microsite->id]) }}" class="button">{{ trans('microsites.actions.edit') }}</a>
                    <span class="description">{{ trans('microsites.hints.edit_microsite') }}</span>
                </p>
                
                <p>
                    <a href="{{ route('microsites.destroy', ['id' => $project->microsite->id]) }}" data-method="delete" data-ask="{{ trans('microsites.actions.delete_ask', ['title' => $project->microsite->title]) }}" class="button danger">{{ trans('microsites.actions.delete') }}</a>
                    <span class="description">{{ trans('microsites.hints.delete_microsite') }}</span>
                </p>
            
            @endif
            
		</div>
		
	</div>


@stop





@section('panels')

@include('panels.generic')

@stop

@section('scripts')

	<script>
	require(["jquery", "DMS"], function($, DMS){
	   $("a[data-method=delete]").click(function(evt){
          
          var $this = $(this);
                    
          DMS.MessageBox.deleteQuestion($this.data('ask'), '').then(function(value){
                
                DMS.MessageBox.wait('Deleting...', '');
                
                return $.ajax({
                    url: $this.attr('href'),
                    type: 'DELETE',
                    dataType: 'json',
                    data: { _token: DMS.csrf() },
                    success: function success(data){
                        
                        if(data.status && data.status === 'ok' && data.message){
                            DMS.MessageBox.success(data.message, '');
                            window.location.reload();
                        }
                        else if(data.error) {
                            DMS.MessageBox.warning(data.error, '');
                        }
                        
                    },
                    error: function fail(obj, err, errText){
                        
                        DMS.MessageBox.error('Cannot complete the microsite delete', errText);
                        
                    }
                });
              
          }, function(dismiss){});
          
          
          evt.preventDefault();
           return false;
       });


        var h = new holmes({

            input: '.js-search-user',

            find: '.userlist .userlist__user',

            placeholder: "{{ trans('projects.labels.search_member_not_found') }}",

            mark: true,

            class: {

                visible: 'visible',

                hidden: 'hidden'

            }

        });

        h.start();
	});
	</script>

@stop