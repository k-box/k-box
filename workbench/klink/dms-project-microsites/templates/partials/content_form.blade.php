<div class="flex flex-col lg:flex-row">
    
    <div class="w-full lg:w-1/2 lg:pr-2">
        <label class="font-bold">{{ trans('microsites.labels.content_en') }}</label>
        
        <input type="hidden" name="content[en]" value="">
        @if(isset($en_entity))
        <input type="hidden" name="content[en][id]" value="{{ $en_entity->id }}">
        @endif
        <input type="hidden" name="content[en][title]" value="{{ old('content.en.title.', isset($en_entity) ? $en_entity->title : $project->name) }}" required />
        <input type="hidden" name="content[en][slug]" readonly value="{{ old('content.en.slug', isset($en_entity) ? $en_entity->slug : str_slug($project->name)) }}" required />
        
        <div class=" mb-4">
            
            <textarea class="form-textarea mt-1 block w-full min-h-screen" type="text" name="content[en][content]" required >{{ old('content.en.content', isset($en_entity) ? $en_entity->content : '') }}</textarea>
            <span class="description text-sm">{!! trans('microsites.hints.page_content') !!}</span>
            
        </div>
    </div>
    
    <div class="w-full lg:w-1/2 lg:pl-2">
        <label class="font-bold">{{ trans('microsites.labels.content_ru') }}</label>
        
        <input type="hidden" name="content[ru]" value="">
        @if(isset($ru_entity))
        <input type="hidden" name="content[ru][id]" value="{{ $ru_entity->id }}">
        @endif
        <input type="hidden" name="content[ru][title]" value="{{ old('content.ru.title', isset($ru_entity) ? $ru_entity->title : $project->name) }}" required />
        <input type="hidden" name="content[ru][slug]" readonly value="{{ old('content.ru.slug', isset($ru_entity) ? $ru_entity->slug : str_slug($project->name)) }}" required />
        
        <div class=" mb-4">
            
            <textarea class="form-textarea mt-1 block w-full min-h-screen" type="text" name="content[ru][content]" required >{{ old('content.ru.content', isset($ru_entity) ? $ru_entity->content : '') }}</textarea>
            <span class="description text-sm">{!! trans('microsites.hints.page_content') !!}</span>
            
        </div>
    </div>
</div>
