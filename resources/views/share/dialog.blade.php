<div class="dialog--share js-sharing c-form">

	<h4 class="dialog__title">{{ trans('share.dialog.title') }}</h4>

	@if(isset($panel_title))
		<h5 class="dialog__subtitle">{{ $panel_title }}</h5>
	@endif


	<div class="dialog__inner">
		
		<div class="error-container js-error-container js-error-container-top"></div>
		{{-- Link Sharing --}}

		@if( !is_null( $sharing_links ) && !empty($sharing_links) && !$is_multiple_selection )

			<div class="copy-links dialog__section js-link-section">

				<div class="dialog__section__inner copy-link ">

					<input type="text" id="document_link" class="copy-link__input" readonly @if($public_link) data-link="{{ $public_link->id }}" @endif data-links="{!! $sharing_links !!}" value="{!! $sharing_links !!}" />

					<button class="button button--larger js-clipboard-btn" data-clipboard-target="#document_link">
						<span class="button__content button__normal">
							@materialicon('content', 'content_copy', 'button__icon')
							{{ trans( $elements_count == 1 ? 'share.document_link_copy' : 'share.document_link_copy_multiple') }}
						</span>
						<span class="button__content button__success">{{ trans('actions.clipboard.copied_title') }}</span>
						<span class="button__content button__error">{{ trans('actions.clipboard.not_copied_title') }}</span>
					</button>
					<a class="button" title="{{ trans($elements_count == 1 ? 'share.send_link' : 'share.send_link_multiple') }}" target="_blank" rel="noopener noreferrer" href="mailto:?body={{ urlencode($sharing_links) }}">
						@materialicon('content', 'mail', 'button__icon')
					</a>


				
					<div class="copy-link__message copy-link__message--error js-copy-message-error">{{trans('actions.clipboard.not_copied_link_text')}}</div>
					
				</div>
			
			</div>
		
		@endif
		{{-- Info: who has access to this item --}}

			{{-- form for adding access for someone --}}

		<div class="dialog__section js-share-section">
			<h6 class="dialog__section__title">{{ trans('share.dialog.section_access_title') }}</h6>
					@unless($is_multiple_selection)

						<select name="linktype" id="linktype" class="js-link-type c-form__input c-form__input--full-width">

							<option value="internal" @unless($public_link) selected @endif>{{ trans('share.dialog.linkshare_members_only') }}</option>
							@unless($has_groups)
								<option value="public" @if($public_link) selected @endif>{{ trans('share.dialog.linkshare_public') }}</option>
							@endunless
						
						</select>

					@endif
					@if($is_multiple_selection)
						<p class="description">{{ trans( $is_multiple_selection ? 'share.dialog.linkshare_multiple_selection_hint' : 'share.dialog.linkshare_hint') }}</p>
					@endif
		
			<div class="dialog__section__inner">
		@if($existing_shares && !$is_multiple_selection)
			<div class="dialog__section js-access-section dialog__section--access">
				<p class="dialog__section__title--access">
					{{ trans($is_collection ? 'share.dialog.collection_is_shared' : 'share.dialog.document_is_shared') }}
					
					@unless($public_link)
					<a href="#" class="js-access">{{trans_choice('share.dialog.users_already_has_access_alternate', count($existing_shares), ['num' => count($existing_shares)]) }}</a>
					@else

					<a href="#" class="js-access">{{trans_choice('share.dialog.users_already_has_access_with_public_link', count($existing_shares), ['num' => count($existing_shares)]) }}</a>
					@endif
				</p>
			
				<div class="dialog__section__inner dialog__section__inner--collapsed shared-list js-access-list">

					@foreach($existing_shares as $share)

						@include('share.partials.shared-list-item', ['item' => $share])
						
					@endforeach

				</div>
			</div>
		@endif

			<div class="dialog__section--add">
				@unless($users->count() == 0)

				<select class="c-form__input js-select-users" name="users[]" id="users" multiple="multiple" style="min-width:auto !important">

					@foreach ($users as $user)
						<option value="{{$user->id}}">{{$user->name}} ({{$user->email}})</option>
					@endforeach
									
				</select>
			
				<button class="js-share button">
					<svg class="btn-icon" style="line-height: 38px;vertical-align: middle;margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
					{{ trans('share.dialog.add_users') }}
				</button>
				@endif

				@if($users->count() == 0)
					{{ trans( $is_collection ? 'share.dialog.collection_already_accessible_by_all_users' : 'share.dialog.document_already_accessible_by_all_users' ) }}
				@endif
			</div>

			</div>
		</div>



		{{-- Publish on Network --}}

		@if(isset($is_network_enabled) && $is_network_enabled)

		<div class="dialog__section js-publish-section">
			<h6 class="dialog__section__title">{{ trans('share.dialog.section_publish_title') }}</h6>
	
			<div class="dialog__section__inner">

				<div class="error-container js-error-container"></div>

				@unless($elements_count == 1)
					{{ trans('share.dialog.publish_multiple_selection_not_supported') }}
				@endif

				@if($is_collection)
					{{ trans('share.dialog.publish_collection_not_supported')}}
				@endif

				@if($elements_count == 1 && !$is_collection)

					@if($is_collection)
						<span class="description">{{ trans('share.dialog.publish_collection') }}</span>
					@endif

					<div class="c-switch js-publish-switch" data-is-public="{{ $is_public ? 'true' : 'false' }}" data-network="{{network_name()}}">

						<div class="c-switch__label js-publish-switch-label">

							@if($publication && $publication->status === 'failed')
								{{ trans('share.dialog.publishing_failed') }}
							@endif

							{{ trans( $is_public ? 'share.dialog.published' : ($has_publishing_request ? 'share.dialog.' . ($publication ? $publication->status:'in_progress')  : 'share.dialog.not_published'), ['network' => network_name()]) }}

						</div>

						<div class="c-switch__buttons">
							@unless(!$can_make_public && $is_public)
							<button @if(!$can_make_public || $has_publishing_request ) disabled @endif class="c-switch__button js-publish-switch-button @unless($is_public || $publication_status === 'published' || $publication_status === 'publishing') c-switch__button--selected @endif" data-action="make_private" title="{{ trans('actions.make_private') }}">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
							</button>
							@endif

							@unless(!$can_make_public && !$is_public)
							<button @if(!$can_make_public || $has_publishing_request ) disabled @endif class="c-switch__button js-publish-switch-button @if($is_public || $publication_status === 'published' || $publication_status === 'publishing') c-switch__button--selected @endif" data-action="make_public" title="{{ trans('networks.publish_to_short') }}">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
							</button>
							@endif

						</div>
					
					</div>

				@endif

			</div>
		</div>

		@endif
	</div>

	<div class="dialog__buttons">
		<button class="button cancel js-cancel">{{ trans('actions.done') }}</button>
	</div>

</div>
