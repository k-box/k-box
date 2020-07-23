<div class="{{ $classes ?? '' }}"  x-data="{ open: false }">

    <button type="button" @click="open = true" class="flex items-center {{ $button_classes ?? 'hover:text-blue-600' }}" @isset($title) title="{{$title}}" @endisset>
        {{ $slot }}
    </button>

    <div @click.away="open = false" x-show.transition="open" style="display: none" class="absolute shadow-lg w-full sm:w-56 p-2 mt-1 text-white bg-white rounded z-10 {{ $position ?? 'right-0' }}">
        {{ $panel }}
    </div>

</div>