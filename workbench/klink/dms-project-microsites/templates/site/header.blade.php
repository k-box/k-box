
<div class="header">
    
    <div class="header__inner">
    
        
        <a class="logo" href="{{ route( 'projects.site', ['slug' => $slug, 'language' => $language] ) }}" title="{{ $title }}">
            @if( isset($logo) && !empty($logo) )
                <img src="{{ $logo }}" width="280">
            @else
                {{ $title }}
            @endif
        </a>
            
        
        <div class="header__navigation">
            @foreach( $available_languages as $lang)
            
                <a class="language @if( $language === $lang ) language--selected @endif" href="{{ route( 'projects.site', ['slug' => $slug, 'language' => $lang] ) }}" title="{{ trans('languages.' . $lang) }}">{{ $lang }}</a>
            
            @endforeach
            
            <a href=" {{ route('documents.groups.show', ['id' => $project_collection_id]) }}">
            @if($isloggedin)
                {{ trans('microsites.actions.view_project_documents') }}
            @else
                {{ trans('auth.login') }}
            @endif
            </a>
        
            <form action="{{ $search_action }}" class="" method="GET">
                <input class="form-input block" type="text" name="s" id="s" placeholder="{{ !$isloggedin ? trans('microsites.actions.search') : trans('microsites.actions.search_project', ['project' => $title]) }}" title="{{ trans('search.form.placeholder') }}">
            </form>
        </div>
        
    
    </div>
    
</div>