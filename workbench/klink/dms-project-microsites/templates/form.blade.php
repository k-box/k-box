
{{ csrf_field() }}
<input type="hidden" name="project" value="{{ $project->id }}">

<div class=" mb-4">
    <label class="font-bold">{{trans('microsites.labels.site_title')}}</label>
    @if( $errors->has('title') )
        <span class="field-error">{{ implode(",", $errors->get('title'))  }}</span>
    @endif
    <input class="form-input block w-full lg:w-1/3" type="text" name="title" value="{{ old('title', isset($microsite) ? $microsite->title : $project->name) }}" required />
    <span class="description">{{ trans('microsites.hints.site_title') }}</span>
</div>

<div class=" mb-4">
    <label class="font-bold">{{trans('microsites.labels.slug')}}</label>
    @if( $errors->has('slug') )
        <span class="field-error">{{ implode(",", $errors->get('slug'))  }}</span>
    @endif
    <input class="form-input block" type="text" name="slug" value="{{ old('slug', isset($microsite) ? $microsite->slug : \Illuminate\Support\Str::slug($project->name) ) }}" required />
    <span class="description">{{ trans('microsites.hints.slug') }}</span>
</div>

<div class=" mb-4">
    <label class="font-bold">{{trans('microsites.labels.logo')}}</label>
    @if( $errors->has('logo') )
        <span class="field-error">{{ implode(",", $errors->get('logo'))  }}</span>
    @endif
    <input class="form-input block w-2/3" type="text" name="logo" value="{{ old('logo', isset($microsite) ? $microsite->logo : '') }}" />
    <span class="description">{{ trans('microsites.hints.logo') }}</span>
</div>    

<div class=" mb-4">
    <label class="font-bold">{{trans('microsites.labels.site_description')}}</label>
    @if( $errors->has('description') )
        <span class="field-error">{{ implode(",", $errors->get('description'))  }}</span>
    @endif
    <textarea class="form-textarea mt-1 block w-2/3" name="description">{{old('description', isset($microsite) ? $microsite->description : '')}}</textarea>
    <span class="description">{{ trans('microsites.hints.description') }}</span>
</div>

<div class=" mb-4">
    <label class="font-bold">{{trans('microsites.labels.default_language')}}</label>
    @if( $errors->has('default_language') )
        <span class="field-error">{{ implode(",", $errors->get('default_language'))  }}</span>
    @endif
    <select class="form-select mt-1 block w-48" id="default_language" name="default_language">
    <option value="en" @if( (isset($microsite) && $microsite->default_language == 'en' ) || old('default_language') == 'en') selected @endif>{{trans('languages.en')}}</option>
    <option value="ru" @if( (isset($microsite) && $microsite->default_language == 'ru' ) || old('default_language') == 'ru') selected @endif>{{trans('languages.ru')}}</option>
    <option value="ky" @if( (isset($microsite) && $microsite->default_language == 'ky' ) || old('default_language') == 'ky') selected @endif>{{trans('languages.ky')}}</option>
    <option value="de" @if( (isset($microsite) && $microsite->default_language == 'de' ) || old('default_language') == 'de') selected @endif>{{trans('languages.de')}}</option>
    <option value="fr" @if( (isset($microsite) && $microsite->default_language == 'fr' ) || old('default_language') == 'fr') selected @endif>{{trans('languages.fr')}}</option>
    <option value="it" @if( (isset($microsite) && $microsite->default_language == 'it' ) || old('default_language') == 'it') selected @endif>{{trans('languages.it')}}</option>

    </select>
    <span class="description">{{ trans('microsites.hints.default_language') }}</span>
</div>




