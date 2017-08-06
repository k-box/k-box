
<h5>{{ trans('microsites.labels.content_en') }}</h5>

<input type="hidden" name="content[en]" value="">
@if(isset($en_entity))
<input type="hidden" name="content[en][id]" value="{{ $en_entity->id }}">
@endif
<input type="hidden" name="content[en][title]" value="{{ old('content.en.title.', isset($en_entity) ? $en_entity->title : $project->name) }}" required />
<input type="hidden" name="content[en][slug]" readonly value="{{ old('content.en.slug', isset($en_entity) ? $en_entity->slug : str_slug($project->name)) }}" required />

<div class="c-form__field">
    <label>{{trans('microsites.labels.page_content')}}</label>
    <span class="description">{!! trans('microsites.hints.page_content') !!}</span>
    
    <textarea class="c-form__input c-form__input--full-width content-textarea" type="text" name="content[en][content]" required >{{ old('content.en.content', isset($en_entity) ? $en_entity->content : '') }}</textarea>
    
</div>

<h5>{{ trans('microsites.labels.content_ru') }}</h5>

<input type="hidden" name="content[ru]" value="">
@if(isset($ru_entity))
<input type="hidden" name="content[ru][id]" value="{{ $ru_entity->id }}">
@endif
<input type="hidden" name="content[ru][title]" value="{{ old('content.ru.title', isset($ru_entity) ? $ru_entity->title : $project->name) }}" required />
<input type="hidden" name="content[ru][slug]" readonly value="{{ old('content.ru.slug', isset($ru_entity) ? $ru_entity->slug : str_slug($project->name)) }}" required />

<div class="c-form__field">
    <label>{{trans('microsites.labels.page_content')}}</label>
    <span class="description">{!! trans('microsites.hints.page_content') !!}</span>
    
    <textarea class="c-form__input c-form__input--full-width content-textarea" type="text" name="content[ru][content]" required >{{ old('content.ru.content', isset($ru_entity) ? $ru_entity->content : '') }}</textarea>
    
</div>
