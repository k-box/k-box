<div>
    <!-- This component includes limits per pages -->

    <div class="page-actions__label" title="{{ trans('documents.filtering.items_per_page_hint') }}">

        <a href="{{ route($routeName,array_merge($pageParams,['n' => 12])) }}" class="button @if(auth()->user()->optionItemsPerPage() == 12) button--selected @endif">12</a>
        <a href="{{ route($routeName,array_merge($pageParams,['n' => 24])) }}" class="button @if(auth()->user()->optionItemsPerPage() == 24) button--selected @endif">24</a>
        <a href="{{ route($routeName,array_merge($pageParams,['n' => 50])) }}" class="button @if(auth()->user()->optionItemsPerPage() == 50) button--selected @endif">50</a>        
    
    </div>
</div>