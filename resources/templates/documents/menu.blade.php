
<div class="sidemenu">

	@if($can_see_private)

		<a href="{{route('documents.index')}}/private" class="sidemenu__item @if(\Request::is('*private') || \Request::is('*documents')) current @endif">
			<span class="sidemenu__item__icon icon-action-black icon-action-black-ic_account_balance_black_24dp"></span> {{trans('documents.menu.private')}}
		</a>		

	@else 

		<a href="{{route('documents.index')}}/personal" class="sidemenu__item @if(\Request::is('*personal') || \Request::is('*documents')) current @endif">
			<span class="sidemenu__item__icon icon-action-black icon-action-black-ic_lock_black_24dp"></span> {{trans('documents.menu.personal')}}
		</a>
	
	@endif

	@if( isset($is_klink_public_enabled) && $is_klink_public_enabled)
	
		<a href="{{ route('documents.index') }}/public" class="hint--bottom sidemenu__item @if(\Request::is('*public')) current @endif" data-hint="{{trans('networks.menu_public_hint', ['network' => network_name() ])}}">
			<span class="sidemenu__item__icon icon-social-black icon-social-black-ic_public_black_24dp"></span> {{ network_name() }}
		</a>
	
	@endif
	
	<a href="{{route('documents.recent')}}" class="hint--bottom sidemenu__item @if(\Request::is('*recent*')) current @endif" data-hint="{{trans('documents.menu.recent_hint')}}">
		<span class="sidemenu__item__icon icon-action-black icon-action-black-ic_schedule_black_24dp"></span> {{trans('documents.menu.recent')}}
	</a>

	<a href="{{route('documents.starred.index')}}" class="hint--bottom sidemenu__item @if(\Request::is('*starred*')) current @endif" data-hint="{{trans('documents.menu.starred_hint')}}">
		<span class="sidemenu__item__icon icon-toggle-black icon-toggle-black-ic_star_black_24dp"></span> {{trans('documents.menu.starred')}}
	</a>


	<a href="{{route('documents.sharedwithme')}}" data-drop="true" data-drop-action="share" class="sidemenu__item @if(\Request::is('*shared-with-me')) current @endif">
		<span class="sidemenu__item__icon icon-social-black icon-social-black-ic_people_black_24dp"></span> {{trans('documents.menu.shared')}}
	</a>
	
	<a href="{{route('documents.trash')}}" data-drop="true" data-drop-action="del" class="sidemenu__item @if(\Request::is('*trash')) current @endif">
		<span class="sidemenu__item__icon icon-action-black icon-action-black-ic_delete_black_24dp"></span> {{trans('documents.menu.trash')}}
	</a>

	<div class="sidemenu__separator"></div>

	@include('groups.tree')
	
</div>

