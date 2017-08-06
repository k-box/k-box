

<div id="document-tree" class="tree-view">

	<div class="tree-header">
	
		<strong><span class="icon icon-action-black icon-action-black-ic_label_black_24dp"></span>Filtered personal collection</strong>
	
	</div>

	<div class="tree-group">
			
		<div class="elements">

			<ul class="clean-ul">
			
				@forelse($facets_groups_personal as $group)
			
					@include('groups.tree-item')
			
				@empty
					
					
			
				@endforelse
			
			</ul>
		</div>
	</div>
	
	
	<div class="tree-header">
	
		<strong><span class="icon icon-action-black icon-action-black-ic_label_black_24dp"></span>Filtered institution collection</strong>
	
	</div>
	
	<div class="tree-group">
			
		<div class="elements">

			<ul class="clean-ul">
			
				@forelse($facets_groups_private as $group)
			
					@include('groups.tree-item')
			
				@empty
					
					
			
				@endforelse
			
			</ul>
		</div>
	</div>
	
</div>

@if(isset($filters)) <p>filters active</p> @endif