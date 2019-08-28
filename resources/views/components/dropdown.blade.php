<div class="{{ $classes ?? '' }}" data-dropdown>
    <button type="button" data-dropdown-trigger class="flex hover:text-blue-600 items-center js-profile-link " @isset($title) title="{{$title}}" @endisset>
        {{ $slot }}
    </button>

    <div data-dropdown-panel class="absolute shadow hidden js-profile w-full sm:w-56 right-0 block p-2 mt-1 text-white bg-gray-700 rounded">
        {{ $panel }}
    </div>

</div>