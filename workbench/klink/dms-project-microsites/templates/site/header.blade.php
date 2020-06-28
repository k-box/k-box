
<header class="">
    
    <div class="relative flex items-center justify-between px-4 lg:px-4 py-4 max-w-screen-xl mx-auto">
    
        <h1 class="text-2xl">
            <a class="no-underline text-black" href="{{ route( 'projects.site', ['slug' => $slug, 'language' => $language] ) }}" title="{{ $title }}">
                @if( isset($logo) && !empty($logo) )
                    <img src="{{ $logo }}" width="280">
                @else
                    {{ $title }}
                @endif
            </a>
        </h1>
            
        
        <div class="header__navigation">
            @foreach( $available_languages as $lang)
            
                <a class="px-3 ml-2 button @if( $language === $lang ) button--selected @endif" href="{{ route( 'projects.site', ['slug' => $slug, 'language' => $lang] ) }}" title="{{ trans('languages.' . $lang) }}">{{ $lang }}</a>
            
            @endforeach
            
            <a class="button ml-4 mr-4" href=" {{ route('documents.groups.show', $project_collection_id) }}">
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
    
</header>
<div class="h-5"></div>