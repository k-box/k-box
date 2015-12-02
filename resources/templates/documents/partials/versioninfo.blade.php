<div class="version-container">

	<h6 id="versions"><span class="icon-action-black icon-action-black-ic_history_black_24dp"></span>
	{{trans('documents.versions.section_title')}}</h6>
	<p class="description">
		{{trans_choice('documents.versions.section_title_with_count', $versions_count, ['number' => $versions_count])}}
	</p>

	@if($can_upload_file)

		@if( $errors->has('document') )
            <span class="field-error">{{ implode(",", $errors->get('document'))  }}</span>
        @endif

		<div class="button ladda-button file-button" id="upload_new">
			<span class="normal">
				<input type="file" name="document" id="document">
				{{trans('documents.versions.new_version_button')}}
			</span>
			<span class="processing">
				{{trans('documents.versions.new_version_button_uploading')}}
			</span>
		</div>
	@endif


	<ul class="clean-ul version-list">
		<?php $counter = $versions_count; ?>
		@foreach($versions as $version)

			
			<li>
				<span>{{$version->name}}</span>
				<span>
					
					@if($counter == $versions_count)
					{{trans('documents.versions.version_current')}}
					@else
					{{trans('documents.versions.version_number', ['number' => $counter])}}
					@endif

					
				</span>
				<span>{{$version->updated_at->diffForHumans()}}</span>
			</li>

			<?php $counter = $counter-1; ?>
		@endforeach
	</ul>

</div>