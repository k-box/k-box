<!-- Project details  panel -->

<!-- Expecting:
	Project instance
	panel_id, if no id is considered as template
 -->

<a href="#close" title="{{trans('panels.close_btn')}}" class="close icon-navigation-white icon-navigation-white-ic_close_white_24dp"></a>

<div class="header">

	<h4 class="title">{{ $project->name }}</h4>

</div>
<div class="actions">
	<a href="{{route('documents.groups.show', $project->collection->id)}}" class="button">{{ trans('projects.show_documents') }}</a>

	@if( !is_null( $project->microsite ) )
		<a target="_blank" href="{{ route('projects.site', ['slug' => $project->microsite->slug]) }}" class="button">{{ trans('microsites.actions.view_site') }}</a>
	@endif
</div>

@if($project->description)
	<div class="meta abstract">
		{{$project->description}}
	</div>
@endif

<div class="meta info">
<p>
<span class="meta-label label">{{trans('projects.labels.created_on')}}&nbsp;</span>{{$project->getCreatedAt()}}
</p>
	<p>
	<span class="meta-label label">{{trans('projects.labels.managed_by')}}&nbsp;</span> {{$project->manager->name}} <a href="mailto:{{$project->manager->email}}">{{$project->manager->email}}</a>
	</p>
	<p>
		<span class="meta-label label">{{trans('projects.labels.documents_count_label')}}&nbsp;</span>{{ trans_choice('projects.labels.documents_count', $project->getDocumentsCount(), ['count' => $project->getDocumentsCount()]) }}
	</p>
</div>

<div class="meta">
	<h6 class="title">{{ trans_choice('projects.labels.user_count', $project->users->count(), ['count' => $project->users->count()]) }}</h6>

	@include('projects.partials.userlist', ['users' => $project_users, 'description' => null, 'empty_message' => trans('projects.no_members'), 'edit' => false ])

</div>