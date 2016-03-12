
<div class="header grid">
    
    <div class="inner">
    
        <div class="four columns">
            <a href="{{ route( 'projects.site', ['slug' => $slug, 'language' => $language] ) }}" title="{{ $title }}">
                @if( isset($logo) && !empty($logo) )
                    <img src="{{ $logo }}" width="280">
                @else
                    {{ $title }}
                @endif
            </a>
        </div>
        
        
        <div class="navigation eight columns">
            
            <div class="search seven columns">
                <form action="{{ $search_action }}" method="GET">
                    <input type="text" name="s" id="s" placeholder="{{ !$isloggedin ? trans('microsites.actions.search') : trans('microsites.actions.search_project', ['project' => $title]) }}" title="{{ trans('search.form.placeholder') }}">
                </form>
            </div>
            
            <div class="two columns language">
                @foreach( $available_languages as $lang)
                
                    <a class="@if( $language === $lang ) selected @endif" href="{{ route( 'projects.site', ['slug' => $slug, 'language' => $lang] ) }}" title="{{ trans('languages.' . $lang) }}">{{ $lang }}</a>
                
                @endforeach
                
                
            </div>
            
            <div class="three columns">
                <a href=" {{ route('documents.groups.show', ['id' => $project_collection_id]) }}">
                @if($isloggedin)
                    {{ trans('microsites.actions.view_project_documents') }}
                @else
                    {{ trans('login.form.submit') }}
                @endif
                </a>
            </div>
            
        </div>
    
    </div>
    
</div>