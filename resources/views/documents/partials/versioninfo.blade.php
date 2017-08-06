<div class="version-container">

	<h4 class="c-section__title" id="versions">@materialicon('action', 'history', 'button__icon')
	{{trans('documents.versions.section_title')}}</h4 >
	<p class="c-section__description">
		{{trans_choice('documents.versions.section_title_with_count', $versions_count, ['number' => $versions_count])}}
	</p>

	@if($can_upload_file)

		@if( $errors->has('document') )
            <span class="field-error">{{ implode(",", $errors->get('document'))  }}</span>
        @endif

		<input type="file" name="document" id="document" style="position: absolute;opacity: 0;z-index:-1">
		<label for="document">
		<div class="button ladda-button file-button" id="upload_new">
			<span class="normal">
				{{trans('documents.versions.new_version_button')}}
			</span>
			<span class="processing">
				{{trans('documents.versions.new_version_button_uploading')}}
			</span>
		</div>
		</label>
	@endif


	<div class="c-form__field version-list">
		
		@foreach($versions as $version)
			
			<div class="version__item @if($loop->first) version__item--current @endif">
				<div class="version__title">
					{{$version->name}}
				
					@if($loop->first)
						<span class="version__badge">	
							{{trans('documents.versions.version_current')}}
						</span>
					@endif

				</div>

				<div class="version__meta">
				
					<div class="version__author">
						@if(!is_null($version->user))
							{{ $version->user->name }}
						@endif
					</div>
					<div class="version__update-date" title="{{ localized_date_full($version->updated_at) }}">
						{{ localized_date_human_diff($version->updated_at) }}
					</div>

				</div>
			</div>

		@endforeach
	</div>

</div>