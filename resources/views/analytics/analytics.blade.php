
@auth
  @if(\KBox\Support\Analytics\Analytics::isActive())

    <!-- Analytics Code -->

    @includeFirst([KBox\Support\Analytics\Analytics::view(), 'analytics.none'], KBox\Support\Analytics\Analytics::configuration())
  
  @endif
@endauth