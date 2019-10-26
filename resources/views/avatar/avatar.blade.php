<div class="{{ isset($inline) && $inline ? 'inline' : 'inline-block' }} rounded-full overflow-hidden flex-shrink-0 h-10 w-10 bg-gray-800 @if(isset($url) && $url) hover:bg-blue-600 @endif">
	@if(isset($url) && $url)
	<a href="{{ $url }}" title="{{ $alt ?? '' }}">
	@endif

	<div class="inline-flex justify-center items-center w-10 h-10">
		@if(isset($image) && is_string($image))
			<img class="block h-10 w-auto" src="{{$image}}">
		@elseif(isset($name) && is_string($name))
			<span class="text-gray-200 text-2xl leading-none">{{ mb_substr(studly_case($name), 0, 1) }}</span>
		@else
			@materialicon('social', 'person', 'inline-block fill-current text-gray-200')
		@endif
	</div>

	@if(isset($url) && $url)
	</a>
	@endif
</div>
