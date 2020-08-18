<div class="hidden lg:block lg:min-h-screen lg:fixed lg:inset-y-0 lg:right-0 lg:w-1/2 {{ $fillColor ?? 'bg-gray-900' }}">
    @if ($hasPicture())
        <img class="w-full object-cover lg:w-full h-full" src="{{ $picture }}" alt="">
    @endif
</div>