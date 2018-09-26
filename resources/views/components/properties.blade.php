<div class="meta info">

    @if(isset($title) && $title)
        <h4 class="c-panel__section">{{$title}}</h4>
    @endif


    @foreach ($properties as $label => $value)

        <div class="c-panel__meta">
            <div class="c-panel__label">
                {{ $label ?? '' }}
            </div>

            {{ $value ?? '' }}
        </div>

    @endforeach
</div>