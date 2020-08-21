
<div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
    <h3 class="text-lg leading-6 font-medium text-gray-900">
        {{ trans('share.dialog.title') }}
        @if(isset($panel_title))
            <span class="mr-1 sm:block">
                {{ $panel_title }}
            </span>
        @endif
    </h3>
            
    {{-- Copy or send Link --}}
    
    @if( !empty( $sharing_links_array ) && !$is_multiple_selection )

        <x-copy class="mt-2" :links="$sharing_links_array" />
    
    @endif

    {{-- Create share --}}

    <div class="h-4"></div>
    
    <h6 class="text-base font-bold">{{ trans('share.dialog.section_access_title') }}</h6>
    
    @unless($is_multiple_selection)

        <p class="text-sm leading-tight text-gray-700">
            @unless($public_link) 
                {{ trans('share.dialog.linkshare_members_only') }} 
            @endunless
            @if($public_link) 
                {{ trans('share.dialog.linkshare_public') }} 
            @endif
        </p>

        

        <div class="mt-2 max-h-24 overflow-y-auto"
            x-data="Fragment({url: '{{ route('shares.users') }}', useCache: true, params: {collections: @json(optional($groups)->pluck('id')), documents: @json(optional($documents)->pluck('id'))}})"
            @share-created.window="refresh"
            >

            <div class="bg-gray-100 p-2 text-sm text-center" x-show="loading && !errors">
                {{ __('Loading who has access list...') }}
            </div>

            <template x-if="errors">
                <div class="c-message c-message--error" x-text="errors"></div>
            </template>

            <div class="">
                <div  x-show="!loading && !errors && !useCache" x-html="content"></div>

                <div x-show="useCache">
                    @include('share.partials.access-list', ['existing_shares' => $existing_shares])
                </div>
            </div>
        </div>
        
    @endunless

    @if($is_multiple_selection)
        <p class="text-sm leading-tight text-gray-700">{{ trans('share.dialog.multiple_selection_hint') }}</p>
    @endif

    

    @if ($can_add_users)
        <div class="mt-2">
            <p class="text-sm leading-tight text-gray-700">{{ __('Share with other K-Box users') }}</p>

            <form method="POST" 
                x-data="AsyncForm()" 
                x-on:submit.prevent="submit" 
                @form-submitted="$dispatch('share-created', $event.detail || {});$dispatch('select-clear', {});"
                class="flex" 
                action="{{route('shares.store')}}">

                @csrf

                <template x-if="errors">
                    <div class="c-message c-message--error" x-text="errors"></div>
                </template>

                <x-select2 name="users" 
                    :documents="$documents"
                    :collections="$groups"
                    class="flex-grow"
                    placeholder="{{ trans('share.dialog.select_users') }}" />

                @foreach ($documents as $document)
                    <input type="hidden" name="documents[]" value="{{ $document->getKey() }}">
                @endforeach
                @foreach ($groups as $group)
                    <input type="hidden" name="groups[]" value="{{ $group->getKey() }}">
                @endforeach
            
                <button type="submit" class="button items-center">
                    <svg class="btn-icon mr-1" style="line-height: 38px;vertical-align: middle;margin-right: 6px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                    {{ trans('share.dialog.add_users') }}
                </button>
            </form>
        </div>
            
    @else
        <div class="c-message c-message--warning mt-2">
            <p class="mb-2">
                {{ trans('share.dialog.cannot_add_users_because_of_project_collection') }}
            </p>
            <p class="flex">
                @if ($can_edit_project && $project)	
                    <a class="block button mr-2 no-underline" href="{{ route('projects.edit', $project) }}" target="_blank" rel="noopener noreferrer">{{ trans('projects.labels.add_users_button') }}</a>
                @endif

                <a href="https://github.com/k-box/k-box/blob/master/docs/user/share.md#sharing-to-a-user" class="button no-underline" target="_blank" rel="noopener noreferrer">{{ trans('actions.more_information') }}</a>
            </p>

        </div>
    @endif

    {{-- Public Link --}}

    @unless($is_collection)
        
        <p class="mt-2 text-sm leading-tight text-gray-700">{{ __('Grant anyone a read-only version using the document link') }}</p>
        
        <form method="POST" 
            x-data="AsyncForm({publicLink: '{{ optional($public_link)->getKey() ?? '' }}', hasPublicLink: {{ $public_link ? 'true' : 'false' }}})" 
            @form-submitted.self="hasPublicLink=!hasPublicLink;publicLink=$event.detail.data.status=='ok' ? null : $event.detail.data.id"
            x-on:submit.prevent="submit" 
            class="mt-2"
            action="{{ route('links.destroy') }}">

                <template x-if="errors">
                    <div class="c-message c-message--error" x-text="errors"></div>
                </template>

                @csrf

                <template x-if="!publicLink">
                    <div>
                        <input type="hidden" name="to_id" value="{{ optional($documents->first())->getKey() }}">
                        <input type="hidden" name="to_type" value="document">
                    </div>
                </template>

                <template x-if="publicLink">
                    <div>
                        <input type="hidden" name="link" :value="publicLink">
                        @method('DELETE')
                    </div>
                </template>

                <button type="submit" class="button inline-flex p-1 whitespace-no-wrap">
                    @materialicon('content', 'link', 'h-6 mr-2') 
                    <div>
                        <span x-show="!publicLink">{{ __('Enable public link') }}</span>
                        <span x-show="publicLink">{{ __('Disable public link') }}</span>
                    </div>
                </button>
            </form>

    @endunless


    {{-- Publication on K-Link --}}

    @if(isset($is_network_enabled) && $is_network_enabled)

        <div class="h-4"></div>
        
        <h6 class="text-base font-bold">{{ trans('share.dialog.section_publish_title') }}</h6>

        @unless($elements_count == 1)
            <p class="text-sm leading-tight text-gray-700">{{ trans('share.dialog.publish_multiple_selection_not_supported') }}</p>
        @endunless

        @if($is_collection)
            <p class="text-sm leading-tight text-gray-700">{{ trans('share.dialog.publish_collection_not_supported')}}</p>
        @endif

        @if($elements_count == 1 && !$is_collection)

            <x-klink-switch class="mt-2" 
                :published="$is_public"
                :publication="$publication"
                :document="$documents->first()"
                :has_publishing_request="$has_publishing_request"
                :network="network_name()" />

        @endif

    @endif

</div>
<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
    <span class="mt-3 flex w-full rounded-md shadow-sm sm:mt-0 sm:w-auto">
        <button type="button" @click="$dispatch('dialog-close', {})" class="inline-flex justify-center w-full rounded-md border border-gray-300 px-4 py-2 bg-white text-base leading-6 font-medium text-gray-700 shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue transition ease-in-out duration-150 sm:text-sm sm:leading-5">
            {{ trans('panels.close_btn') }}
        </button>
    </span>
</div>
