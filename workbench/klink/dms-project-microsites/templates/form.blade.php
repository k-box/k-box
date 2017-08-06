
{{ csrf_field() }}
<input type="hidden" name="project" value="{{ $project->id }}">

<div class="c-form__field">
    <label>{{trans('microsites.labels.site_title')}}</label>
    @if( $errors->has('title') )
        <span class="field-error">{{ implode(",", $errors->get('title'))  }}</span>
    @endif
    <input class="c-form__input c-form__input--larger" type="text" name="title" value="{{ old('title', isset($microsite) ? $microsite->title : $project->name) }}" required />
    <span class="description">{{ trans('microsites.hints.site_title') }}</span>
</div>

<div class="c-form__field">
    <label>{{trans('microsites.labels.slug')}}</label>
    @if( $errors->has('slug') )
        <span class="field-error">{{ implode(",", $errors->get('slug'))  }}</span>
    @endif
    <input class="c-form__input" type="text" name="slug" value="{{ old('slug', isset($microsite) ? $microsite->slug : str_slug($project->name) ) }}" required />
    <span class="description">{{ trans('microsites.hints.slug') }}</span>
</div>


<div class="c-form__field">
    <label>{{trans('microsites.labels.logo')}}</label>
    @if( $errors->has('logo') )
        <span class="field-error">{{ implode(",", $errors->get('logo'))  }}</span>
    @endif
    <input class="c-form__input c-form__input--larger" type="text" name="logo" value="{{ old('logo', isset($microsite) ? $microsite->logo : '') }}" />
    <span class="description">{{ trans('microsites.hints.logo') }}</span>
</div>    

<div class="c-form__field">
    <label>{{trans('microsites.labels.site_description')}}</label>
    @if( $errors->has('description') )
        <span class="field-error">{{ implode(",", $errors->get('description'))  }}</span>
    @endif
    <textarea class="c-form__input c-form__input--larger" name="description">{{old('description', isset($microsite) ? $microsite->description : '')}}</textarea>
</div>

<div class="c-form__field">
    <label>{{trans('microsites.labels.publishing_box')}}</label>
    <button type="submit" class="button button--primary">{{ trans( isset($microsite) ? 'microsites.actions.save' : 'microsites.actions.publish') }}</button><br/>{{trans('actions.or_alt')}} <a href="{{ route('projects.show', ['id' => $project->id]) }}">{{trans('microsites.labels.cancel_and_back')}}</a>
</div>

<div class="c-form__field">
    <label>{{trans('microsites.labels.default_language')}}</label>
    @if( $errors->has('default_language') )
        <span class="field-error">{{ implode(",", $errors->get('default_language'))  }}</span>
    @endif
    <select class="c-form__input" id="default_language" name="default_language">
    <option value="en" @if( (isset($microsite) && $microsite->default_language == 'en' ) || old('default_language') == 'en') selected @endif>{{trans('languages.en')}}</option>
    <option value="ru" @if( (isset($microsite) && $microsite->default_language == 'ru' ) || old('default_language') == 'ru') selected @endif>{{trans('languages.ru')}}</option>
    <option value="ky" @if( (isset($microsite) && $microsite->default_language == 'ky' ) || old('default_language') == 'ky') selected @endif>{{trans('languages.ky')}}</option>
    <option value="de" @if( (isset($microsite) && $microsite->default_language == 'de' ) || old('default_language') == 'de') selected @endif>{{trans('languages.de')}}</option>
    <option value="fr" @if( (isset($microsite) && $microsite->default_language == 'fr' ) || old('default_language') == 'fr') selected @endif>{{trans('languages.fr')}}</option>
    <option value="it" @if( (isset($microsite) && $microsite->default_language == 'it' ) || old('default_language') == 'it') selected @endif>{{trans('languages.it')}}</option>

    </select>
    <span class="description">{{ trans('microsites.hints.default_language') }}</span>
</div>
