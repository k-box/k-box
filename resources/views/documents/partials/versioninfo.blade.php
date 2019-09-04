<div class="version-container">

	<h4 class="c-section__title" id="versions">@materialicon('action', 'history', 'button__icon c-section__icon')
		{{trans_choice('documents.versions.section_title_with_count', $versions_count, ['number' => $versions_count])}}
	</h4>

	@if($can_upload_file)

		<div class="version-actions">

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

		</div>
	@endif


	<div class="c-form__field version-list">
		
		@foreach($versions as $version)
			
			<div class="version__item @if($loop->first) version__item--current @endif">
				<div>
					<div class="version__title" title="{{$version->name}}">
						@if($loop->first)
							{{$version->name}}
						@else
							<a href="{{DmsRouting::preview($document, $version)}}" target="_blank">{{$version->name}}</a>
						@endif
					</div>

					<div class="version__meta">
					
						<div class="version__author">

							@can('see_uploader', $version)
								{{ optional($version->user)->name }},&nbsp;
							@else 
								@component('components.undisclosed_user', ['after' => ', '])
									
								@endcomponent
							@endcan

							<span title="{{ localized_date_full($version->updated_at) }}">{{ localized_date_human_diff($version->updated_at) }}</span>
						</div>

					</div>
				</div>
				
				@if($loop->first)
				<div>
					@materialicon('image', 'navigate_before', 'version__current')
				</div>
				@endif
				
				@unless($loop->first)
				<div>
					<button class="button button--ghost" data-action="restoreVersion" data-version-title="{{$version->name}}" data-document-id="{{ $document->id }}" data-version-id="{{ $version->uuid }}">{{ trans('actions.restore') }}</button>
				</div>
				@endunless
			</div>

		@endforeach
	</div>

</div>