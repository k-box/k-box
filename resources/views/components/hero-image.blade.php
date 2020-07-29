@props([
    'hasPicture' => true,
    'picture' => 'https://images.unsplash.com/photo-1563654727148-d7e9d1ed2a86?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=2850&q=80',
])

<div class="hidden lg:block lg:min-h-screen lg:fixed bg-gray-900 lg:inset-y-0 lg:right-0 lg:w-1/2">
    @if (!!$hasPicture)
        <img class="w-full object-cover lg:w-full h-full" src="{{ $picture }}" alt="">
    @endif
</div>