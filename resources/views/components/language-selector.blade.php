@if ($isDropdown())


    @component('components.dropdown', [
        'classes' => $attributes->whereStartsWith('class')->first() ?? '',
        'button_classes' => 'button',
        'title' => trans('profile.labels.language')
    ])

        <div class="">
            <span class="font-mono inline-block mr-2">{{ $current }}</span> {{trans("languages.$current", [], $current)}}
        </div>

        @materialicon('navigation', 'arrow_drop_down', ['class' => 'inline fill-current arrow', ':class' => "{ 'rotate-180': open }"])

        @slot('panel')

            <form method="post" id="language-selector-dropdown" class="" action="{{route('profile.language.update')}}">

                @csrf
                
                @method('PUT')

                @foreach ($languages as $language)
                    <label class="no-underline block p-2 -mx-2 mb-1 text-black hover:bg-blue-100 active:bg-blue-200 focus:bg-blue-100 focus:outline-none" title="{{trans("languages.$language")}}">
                        <input onclick="document.getElementById('language-selector-dropdown').submit()" type="radio" name="language" value="{{$language}}" id="" @if($current==$language) checked @endif>
                        {{trans("languages.$language", [], $language)}}
                    </label>
                @endforeach

                <div class="mt-2 hidden">
                    <button type="submit" class="button hidden">{{trans('profile.change_language_btn')}}</button>
                </div>
            </form>
            
        @endslot
        
    @endcomponent

@else

    <form method="post" class="" action="{{route('profile.language.update')}}">

        @csrf
        
        @method('PUT')

        <select class="form-select block mt-1" name="language" autocomplete="off">
            {{-- 
                autocomplete="off" force Firefox to not cache the selected value and use always the latest
                https://stackoverflow.com/questions/10870567/firefox-not-refreshing-select-tag-on-page-refresh
            --}}
            
            @foreach ($languages as $language)
                <option value="{{$language}}" @if($current==$language) selected @endif>{{trans("languages.$language", [], $language)}}</option>
            @endforeach

        </select>

        <div class="mt-2">
            <button type="submit" class="button">{{trans('profile.change_language_btn')}}</button>
        </div>
    </form>
@endif
