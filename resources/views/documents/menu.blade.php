
<div class="navigation navigation--secondary">

	<a href="{{route('documents.index')}}" class="navigation__item navigation__item--link @if(\Request::is('documents')) navigation__item--current @endif">
		@materialicon('action', 'lock', 'inline-block navigation__item__icon'){{trans('documents.menu.personal')}}
	</a>

	@if( isset($is_klink_public_enabled) && $is_klink_public_enabled)
	
		<a href="{{ route('documents.index') }}/public" class="hint--bottom navigation__item navigation__item--link @if(\Request::is('*public')) navigation__item--current @endif" data-hint="{{trans('networks.menu_public_hint', ['network' => network_name() ])}}">
			@materialicon('social', 'public', 'inline-block navigation__item__icon'){{ network_name() }}
		</a>
	
	@endif
	
	<a href="{{route('documents.recent')}}" class="hint--bottom navigation__item navigation__item--link @if(\Request::is('*recent*')) navigation__item--current @endif" data-hint="{{trans('documents.menu.recent_hint')}}">
		@materialicon('action', 'schedule', 'inline-block navigation__item__icon'){{trans('documents.menu.recent')}}
	</a>

	<a href="{{route('documents.starred.index')}}" class="hint--bottom navigation__item navigation__item--link @if(\Request::is('*starred*')) navigation__item--current @endif" data-hint="{{trans('documents.menu.starred_hint')}}">
		@materialicon('toggle', 'star', 'inline-block navigation__item__icon'){{trans('documents.menu.starred')}}
	</a>


	<a href="{{route('documents.sharedwithme')}}" data-drop="true" data-drop-action="share" class="navigation__item navigation__item--link @if(\Request::is('*shared-with-me') || (isset($context_group_shared) && $context_group_shared)) navigation__item--current @endif">
		@materialicon('social', 'people', 'inline-block navigation__item__icon'){{trans('documents.menu.shared')}}
	</a>

	@if( flags('plugins') && plugins('k-box-kbox-plugin-geo'))
	
		<a href="{{ route('plugins.k-box-kbox-plugin-geo.geodocuments') }}" class="hint--bottom navigation__item navigation__item--link @if(\Request::is('*geoplugin*')) navigation__item--current @endif" data-hint="{{trans('geo::section.page_hint')}}">
			@materialicon('maps', 'map', 'inline-block navigation__item__icon'){{ trans('geo::section.page_title') }}
		</a>
	
	@endif
	
	<a href="{{route('documents.trash')}}" data-drop="true" data-drop-action="del" class="navigation__item navigation__item--link @if(\Request::is('*trash')) navigation__item--current @endif">
		@materialicon('action', 'delete', 'inline-block navigation__item__icon'){{trans('documents.menu.trash')}}
	</a>

	@include('groups.tree')
	
</div>

