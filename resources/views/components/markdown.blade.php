<div {{ $attributes->merge(['class' => 'prose']) }}>
    {!! trim($convert($slot ?? $value)) !!}
</div>