@props(['links'])


<div {{ $attributes->merge(['class' => '']) }} 
    x-data="CopyToClipboard({links: '{{ implode('&#13;&#10;', $links) }}'})">

    <div class="form-input border-0 py-1 px-0 flex flex-nowrap items-center">
        
        <div class="flex-grow mr-2 min-w-0 truncate">

            <input type="text" class="block w-full" x-text="links" x-ref="select" value="{{ implode('&#13;&#10;', $links) }}" readonly  />
        </div>
        
        <div class="flex">
            <button type="button" class="button inline-flex  items-center mr-2" @click="copy" :class="{ 'bg-green-300 border-green-700': copied === true }" title="{{ trans('share.document_link_copy') }}">
                <span class="" x-show="!copied">@materialicon('content', 'content_copy', '')</span>
                <span class="" x-show="copied">@materialicon('action', 'done', '')</span>
                <span class="hidden md:inline md:ml-1" x-show="!copied">{{ trans( count($links) == 1 ? 'share.document_link_copy' : 'share.document_link_copy_multiple') }}</span>
                <span class="hidden md:inline md:ml-1" x-show="copied">{{ trans('actions.clipboard.copied_title') }}</span>
            </button>
            <a class="button items-center" title="{{ trans(count($links) == 1 ? 'share.send_link' : 'share.send_link_multiple') }}" target="_blank" rel="noopener noreferrer" href="mailto:?body={{ urlencode(implode('&#13;&#10;', $links)) }}">
                @materialicon('content', 'mail', 'button__icon mr-0')
            </a>
        </div>
    </div>
    <span class="field-error" x-show="error">{{trans('actions.clipboard.not_copied_link_text')}}</span>

</div>