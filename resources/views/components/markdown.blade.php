<div {{ $attributes->merge(['class' => 'markdown']) }}>
    {!! trim($convert($slot ?? $value)) !!}
</div>