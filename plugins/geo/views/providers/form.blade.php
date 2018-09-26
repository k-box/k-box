<div class="c-form__field">
<label for="type">@lang('geo::settings.providers.attributes.type')</label>
<span class="description">@lang('geo::settings.providers.attributes.type_description')</span>
@if( isset($errors) && $errors->has('type') )
<span class="field-error">{{ implode(",", $errors->get('type'))  }}</span>
@endif
<select class="c-form__input c-form__input--larger" required name="type" id="type" @if(isset($provider)) disabled @endif>
    <option value="tile" @if(old('type', $provider['type'] ?? '') === 'tile') selected @endif>@lang('geo::settings.providers.types.tile')</option>
    <option value="wms" @if(old('type', $provider['type'] ?? '') === 'wms') selected @endif>@lang('geo::settings.providers.types.wms')</option>
</select>
</div>

<div class="c-form__field">
<label for="label">@lang('geo::settings.providers.attributes.label')</label>
<span class="description">@lang('geo::settings.providers.attributes.label_description')</span>
@if( isset($errors) && $errors->has('label') )
<span class="field-error">{{ implode(",", $errors->get('label'))  }}</span>
@endif
<input class="c-form__input c-form__input--larger" type="text" required name="label" id="label" value="{{old('label', $provider['label'] ?? '')}}">
</div>

<div class="c-form__field">
<label for="url">@lang('geo::settings.providers.attributes.url')</label>
<span class="description">@lang('geo::settings.providers.attributes.url_description')</span>
@if( isset($errors) && $errors->has('url') )
    <span class="field-error">{{ implode(",", $errors->get('url'))  }}</span>
@endif
<input class="c-form__input c-form__input--larger" type="text" required name="url" id="url" value="{{old('url', $provider['url'] ?? '')}}">
</div>

<div class="c-form__field">
<label for="attribution">@lang('geo::settings.providers.attributes.attribution')</label>
<span class="description">@lang('geo::settings.providers.attributes.attribution_description')</span>
@if( isset($errors) && $errors->has('attribution') )
    <span class="field-error">{{ implode(",", $errors->get('attribution'))  }}</span>
@endif
<input class="c-form__input c-form__input--larger" type="text" required name="attribution" id="attribution" value="{{old('attribution', $provider['attribution'] ?? '')}}">
</div>

<div class="c-form__field">
<label for="maxZoom">@lang('geo::settings.providers.attributes.maxZoom')</label>
<span class="description">@lang('geo::settings.providers.attributes.maxZoom_description')</span>
@if( isset($errors) && $errors->has('maxZoom') )
    <span class="field-error">{{ implode(",", $errors->get('maxZoom'))  }}</span>
@endif
<input class="c-form__input c-form__input--larger" type="text" name="maxZoom" id="maxZoom" value="{{old('maxZoom', $provider['maxZoom'] ?? '20')}}">
</div>

@if(!isset($provider) || (isset($provider) && $provider['type'] === 'wms'))

<div class="c-form__field">
<label for="layers">@lang('geo::settings.providers.attributes.layers')</label>
<span class="description">@lang('geo::settings.providers.attributes.layers_description')</span>
@if( isset($errors) && $errors->has('layers') )
    <span class="field-error">{{ implode(",", $errors->get('layers'))  }}</span>
@endif
<input class="c-form__input c-form__input--larger" type="text" name="layers" id="layers" value="{{old('layers', $provider['layers'] ?? '')}}">
</div>

@endif

<div class="c-form__field">
<label for="subdomains">@lang('geo::settings.providers.attributes.subdomains')</label>
<span class="description">@lang('geo::settings.providers.attributes.subdomains_description')</span>
@if( isset($errors) && $errors->has('subdomains') )
    <span class="field-error">{{ implode(",", $errors->get('subdomains'))  }}</span>
@endif
<input class="c-form__input c-form__input--larger" type="text" name="subdomains" id="subdomains" value="{{old('subdomains', $provider['subdomains'] ?? '')}}">
</div>