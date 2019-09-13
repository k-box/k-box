<div class="{{ $classes ?? '' }}">
    
    <div class="{{ $label_class ?? '' }}">
        {{ $slot }}
    </div>

    <div class="{{ $height ?? 'h-2'}} w-full bg-gray-300 rounded-lg overflow-hidden {{ $shadow ?? 'shadow'}}">
        <div class="{{ $height ?? 'h-2'}} bg-accent-500 transition " style="width:{{$percentage ?? 0 }}%"></div>
    </div>

</div>