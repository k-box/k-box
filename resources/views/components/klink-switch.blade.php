@props([
    'document',
    'published',
    'network',
    'publication' => null,
    'network',
])

@php
    $is_published = $document->is_public || optional($publication)->status === 'published';
    $is_publishing = optional($publication)->status === 'publishing';
    $is_unpublishing = optional($publication)->status === 'unpublishing';
    $is_error = optional($publication)->status === 'failed';
@endphp

<div {{ $attributes->merge(['class' => 'c-switch']) }} 
    x-data="KlinkSwitch({document: '{{ $document->getKey() }}', published: {{ $is_published ? 'true' : 'false' }}, publishing: {{ $is_publishing ? 'true' : 'false' }}, unpublishing: {{ $is_unpublishing ? 'true' : 'false' }}, error: '{{ $is_error ? trans('share.dialog.publishing_failed') : false }}'})">

    <div class="c-switch__label">
        <p x-cloak x-show="published">{{ trans('share.dialog.published', ['network' => $network]) }}</p>
        <p x-cloak x-show="!published">{{ trans('share.dialog.not_published', ['network' => $network]) }}</p>
        <p x-cloak x-show="publishing">{{ trans('share.dialog.publishing') }}</p>
        <p x-cloak x-show="unpublishing">{{ trans('share.dialog.unpublishing') }}</p>
        <p x-cloak x-show="error" class="text-red-500">
            <template x-if="error === true">
                <span>{{ trans('share.dialog.publishing_failed') }}</span>
            </template>
            <template x-if="error">
                <span x-text="error"></span>
            </template>
        </p>
    </div>

    @can('create', \KBox\Publication::class)
        
        <div class="c-switch__buttons">
            
            <button type="submit" class="c-switch__button" @click="unpublish" :class="{'c-switch__button--selected': !published}" title="{{ trans('actions.make_private') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/></svg>
            </button>
            
            <button type="submit" class="c-switch__button text-right" @click="publish"  :class="{'c-switch__button--selected': published}" title="{{ trans('networks.publish_to_short') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 17.93c-3.95-.49-7-3.85-7-7.93 0-.62.08-1.21.21-1.79L9 15v1c0 1.1.9 2 2 2v1.93zm6.9-2.54c-.26-.81-1-1.39-1.9-1.39h-1v-3c0-.55-.45-1-1-1H8v-2h2c.55 0 1-.45 1-1V7h2c1.1 0 2-.9 2-2v-.41c2.93 1.19 5 4.06 5 7.41 0 2.08-.8 3.97-2.1 5.39z"/></svg>
            </button>
            

        </div>
    @endcan

</div>
