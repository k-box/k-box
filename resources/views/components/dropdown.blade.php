<div class="{{ $classes ?? '' }}"  x-data="{ open: false }">

    <button type="button" @click="open = true" class="flex hover:text-blue-600 items-center js-profile-link " @isset($title) title="{{$title}}" @endisset>
        {{ $slot }}
    </button>

    <div @click.away="open = false" x-show.transition="open" class="absolute shadow-lg js-profile w-full sm:w-56 right-0 p-2 mt-1 text-white bg-white rounded">
        {{ $panel }}
    </div>

</div>