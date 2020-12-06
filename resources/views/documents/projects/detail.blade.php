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

	<x-copy-button :links="[route('documents.groups.show', $project->collection->id)]" />

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
		<span class="c-panel__label">{{trans('projects.labels.created_on')}}</span>@date($project->created_at)
	</div>
	<div class="c-panel__meta">
		<span class="c-panel__label">{{trans('projects.labels.managed_by')}}</span>{{$project->manager->name}} <a href="mailto:{{$project->manager->email}}">{{$project->manager->email}}</a>
	</div>
	<div class="c-panel__meta">
		<span class="c-panel__label">{{trans('projects.labels.documents_count_label')}}</span>{{ trans_choice('projects.labels.documents_count', $project->getDocumentsCount(), ['count' => $project->getDocumentsCount()]) }}
	</div>

	<div class="">
		<h4 class="c-panel__section">{{ trans_choice('projects.labels.user_count', $project->users->count(), ['count' => $project->users->count()]) }}</h4>

		@include('projects.partials.userlist', ['users' => $project_users, 'description' => null, 'empty_message' => trans('projects.no_members'), 'edit' => false ])

	</div>

</div>