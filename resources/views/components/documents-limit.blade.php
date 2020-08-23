<div>
    <!-- This component includes limits per pages -->
    <div class="page-actions__label" title="{{ trans('documents.filtering.items_per_page_hint') }}">


        <a href="{{ route($routeName, array_merge($routeParamId,['range' => $range, 'n' => 12], $search_replica_parameters)) }}" class="button @if(auth()->user()->optionItemsPerPage() == 12) button--selected @endif">12</a>
        <a href="{{ route($routeName, array_merge($routeParamId,['range' => $range, 'n' => 24], $search_replica_parameters)) }}" class="button @if(auth()->user()->optionItemsPerPage() == 24) button--selected @endif">24</a>
        <a href="{{ route($routeName, array_merge($routeParamId, ['range' => $range, 'n' => 50], $search_replica_parameters)) }}" class="button @if(auth()->user()->optionItemsPerPage() == 50) button--selected @endif">50</a>
         
    </div>
</div>