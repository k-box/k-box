<div>
    <!-- This component includes limits per pages -->

    <div class="page-actions__label switcher flex flex-nowrap m-2"  title="{{ trans('documents.filtering.items_per_page_hint') }}">

        <a href="{{ route($routeName,array_merge($pageParams,['n' => 12])) }}" class="button @if($optionItemsPerPage == 12) button--selected @endif p-2 ml-0 rounded-r-none" data-list="details">12</a>
        <a href="{{ route($routeName,array_merge($pageParams,['n' => 24])) }}" class="button @if($optionItemsPerPage == 24) button--selected @endif p-2 ml-0 rounded-none">24</a>
        <a href="{{ route($routeName,array_merge($pageParams,['n' => 50])) }}" class="button @if($optionItemsPerPage == 50) button--selected @endif p-2 ml-0 rounded-l-none">50</a>        
    
    </div>
</div>