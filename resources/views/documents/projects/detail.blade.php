<!-- Project details  panel -->

<!-- Expecting:
	Project instance
	panel_id, if no id is considered as template
 -->



<div class="c-panel__header">

	<h4 class="c-panel__title">{{ $project->name }}</h4>

</div>
<div class="c-panel__actions">
	<a href="{{route('documents.groups.show', $project->collection->id)}}" class="button">{{ trans('projects.show_documents') }}</a>

	@if( flag('microsite') && !is_null( $project->microsite ) )
		<a target="_blank" href="{{ route('projects.site', ['slug' => $project->microsite->slug]) }}" class="button">{{ trans('microsites.actions.view_site') }}</a>
	@endif

	@if(auth()->check() && auth()->user()->id === $project->user_id )

		<a href="{{route('projects.edit', $project->id)}}" class="button">{{ trans('projects.edit_button') }}</a>
	@endif
</div>

<div class="c-panel__data">

	@if($project->description)
		<div class="c-panel__meta">
			{{$project->description}}
		</div>
	@endif

	<div class="c-panel__meta">
		<span class="c-panel__label">{{trans('projects.labels.created_on')}}</span>{{$project->getCreatedAt()}}
	</div>
	<div class="c-panel__meta">
		<span class="c-panel__label">{{trans('projects.labels.managed_by')}}</span>{{$project->manager->name}} <a href="mailto:{{$project->manager->email}}">{{$project->manager->email}}</a>
	</div>
	<div class="c-panel__meta">
		<span class="c-panel__label">{{trans('projects.labels.documents_count_label')}}</span>{{ trans_choice('projects.labels.documents_count', $project->getDocumentsCount(), ['count' => $project->getDocumentsCount()]) }}
	</div>


	@if(auth()->check() && flag('microsite') && (auth()->user()->id === $project->user_id || !is_null( $project->microsite )))

	<div class="c-panel__meta">
		<h4 class="c-panel__section">{!! trans('microsites.labels.microsite') !!}</h4>
			<span class="description">{{trans('microsites.hints.what')}}</span>
            
            @if( is_null( $project->microsite ) && auth()->user()->id === $project->user_id)
            
                <p>
                    <a id="microsite_create" href="{{ route('microsites.create', ['project' => $project->id]) }}" class="button">{{ trans('microsites.actions.create') }}</a>
                    <span class="description">{{ trans('microsites.hints.create_for_project') }}</span>
                </p>
            
            @else 
            
				@if( $project->microsite )
					<p>
						<a target="_blank" rel="nofollow noopener" href="{{ route('projects.site', ['slug' => $project->microsite->slug]) }}" class="button">{{ trans('microsites.actions.view_site') }}</a>
					</p>
				@endif

				@if( auth()->user()->id === $project->user_id && $project->microsite)
            
					<p>
						<a id="microsite_edit" href="{{ route('microsites.edit', ['id' => $project->microsite->id]) }}" class="button">{{ trans('microsites.actions.edit') }}</a>
						<span class="description">{{ trans('microsites.hints.edit_microsite') }}</span>
					</p>
					
					<p>
						<a href="{{ route('microsites.destroy', ['id' => $project->microsite->id]) }}" data-project="{{$project->id}}" data-microsite="{{ $project->microsite->id }}" data-action="micrositeDelete" data-ask="{{ trans('microsites.actions.delete_ask', ['title' => $project->microsite->title]) }}" class="button button--danger">{{ trans('microsites.actions.delete') }}</a>
						<span class="description">{{ trans('microsites.hints.delete_microsite') }}</span>
					</p>

				@endif
            
            @endif
            
		</div>
	@endif

	<div class="c-panel__meta">
		<h4 class="c-panel__section">{{ trans_choice('projects.labels.user_count', $project->users->count(), ['count' => $project->users->count()]) }}</h4>

		@include('projects.partials.userlist', ['users' => $project_users, 'description' => null, 'empty_message' => trans('projects.no_members'), 'edit' => false ])

	</div>

</div>