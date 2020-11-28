@props(['links'])

<button type="button" {{ $attributes->merge(['class' => 'button inline-flex items-center']) }} 
    x-data="CopyToClipboard({links: '{{ implode('&#13;&#10;', $links) }}'})"
    @click="copy" 
    :class="{ 'bg-green-300 border-green-700': copied === true }" 
    title="{{ trans('share.document_link_copy') }}">
        <span class="" x-show="!copied">@materialicon('content', 'content_copy', '')</span>
        <span class="" x-show="copied">@materialicon('action', 'done', '')</span>
        <span class="hidden md:inline md:ml-1" x-show="!copied">{{ trans( count($links) == 1 ? 'share.document_link_copy' : 'share.document_link_copy_multiple') }}</span>
        <span class="hidden md:inline md:ml-1" x-show="copied">{{ trans('actions.clipboard.copied_title') }}</span>
        <span class="field-error" x-show="error">{{trans('actions.clipboard.not_copied_link_text')}}</span>
</button>