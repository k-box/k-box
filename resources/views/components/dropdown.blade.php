<div class="{{ $classes ?? '' }}" data-dropdown>

    <div data-dropdown-obscurer class="fixed top-0 left-0 w-screen h-screen hidden bg-black opacity-25"></div>

    <button type="button" data-dropdown-trigger class="flex hover:text-blue-600 items-center js-profile-link " @isset($title) title="{{$title}}" @endisset>
        {{ $slot }}
    </button>

    <div data-dropdown-panel class="absolute shadow-lg hidden js-profile w-full sm:w-56 right-0 block p-2 mt-1 text-white bg-white rounded">
        {{ $panel }}
    </div>

</div>