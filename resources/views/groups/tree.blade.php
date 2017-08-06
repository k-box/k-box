
<div id="document-tree" class="tree-view">
	
	@if($user_can_see_private_groups)
	
	
	<a href="{{ route('documents.projects.index') }}" class="navigation__item navigation__item--link @if(\Request::is('*projects')) navigation__item--current @endif"><strong>{{trans('projects.page_title')}}</strong></a>

	<div class="tree-group">
			
		<div class="elements">
			
			
			
			@forelse($private_groups as $group)
		
				@include('groups.tree-item')
				
			@empty
			
				<p>{{trans('groups.collections.empty_private_msg')}}</p>
				
			@endforelse

			
		</div>
	</div>
	@endif
	
	@if($user_can_edit_personal_groups)
	
	<div class="navigation__item">
	
		<strong>@materialicon('action', 'label', 'navigation__item__icon') {{trans('groups.collections.personal_title')}}</strong>
	
		<div class="navigation__actions">
			@if($user_can_edit_personal_groups || $user_can_see_private_groups)
			<button rv-on-click="menu.createGroup" class="navigation__action" data-isprivate="true" title="{{trans('actions.create_collection_btn')}}">
				@materialicon('content', 'add_circle_outline', 'btn-icon')
			</button>
			@endif 

		</div>
	
	</div>
	

	<div class="tree-group">
			
		<div class="elements">
			
				@forelse($personal_groups as $group)
			
					@include('groups.tree-item')
			
				@empty
					
					<div class="empty empty--small">
						@materialicon('action', 'label_outline', 'empty__icon')

						<p class="empty__description">{{trans('groups.collections.description')}}</p>
						
						@if($user_can_edit_personal_groups)
						<button rv-on-click="menu.createGroup" data-isprivate="true" title="{{trans('actions.create_collection_btn')}}" class="button">
							@materialicon('content', 'add_circle_outline', 'button__icon'){{trans('actions.create_collection_btn')}}
						</button>
						@endif
					</div>

					
						
			
				@endforelse
			
			
		</div>
	</div>

	@endif

</div>
