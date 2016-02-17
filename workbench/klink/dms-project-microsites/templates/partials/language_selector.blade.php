<label for="language">{{trans('documents.edit.language_label')}}</label>
@if( $errors->has('language') )
    <span class="field-error">{{ implode(",", $errors->get('language'))  }}</span>
@endif
<select class="u-full-width" id="language" name="language" @if(!$can_edit_document) disabled @endif>
<option value="en" @if($document->language == 'en') selected @endif>{{trans('languages.en')}}</option>
<option value="ru" @if($document->language == 'ru') selected @endif>{{trans('languages.ru')}}</option>
<option value="kg" @if($document->language == 'kg') selected @endif>{{trans('languages.kg')}}</option>
<option value="de" @if($document->language == 'de') selected @endif>{{trans('languages.de')}}</option>
<option value="fr" @if($document->language == 'fr') selected @endif>{{trans('languages.fr')}}</option>
<option value="it" @if($document->language == 'it') selected @endif>{{trans('languages.it')}}</option>

</select>