
<div class="documents-menu">
	<ul class="documents-navigation">
		
		
		@if($can_see_private)

		<li>
			<a href="{{route('documents.index')}}/private" class="menu-el @if(\Request::is('*private') || \Request::is('*documents')) current @endif">
				<span class="menu-icon icon-action-black icon-action-black-ic_account_balance_black_24dp"></span> {{trans('documents.menu.private')}}
			</a>
		</li>

		@else 

		<li>
			<a href="{{route('documents.index')}}/personal" class="menu-el @if(\Request::is('*personal') || \Request::is('*documents')) current @endif">
				<span class="menu-icon icon-action-black icon-action-black-ic_lock_black_24dp"></span> {{trans('documents.menu.personal')}}
			</a>
		</li>

		@endif

        @if( isset($is_klink_public_enabled) && $is_klink_public_enabled)
		<li>
			<a href="{{ route('documents.index') }}/public" class="menu-el @if(\Request::is('*public')) current @endif">
				<span class="menu-icon icon-social-black icon-social-black-ic_public_black_24dp"></span> {{trans('documents.menu.public')}}
			</a>
		</li>
        @endif
		<li>
			<a href="{{route('documents.recent')}}" class="hint--bottom menu-el @if(\Request::is('*recent')) current @endif" data-hint="{{trans('documents.menu.recent_hint')}}">
				<span class="menu-icon icon-action-black icon-action-black-ic_schedule_black_24dp"></span> {{trans('documents.menu.recent')}}
			</a>
		</li>
		<li>
			<a href="{{route('documents.starred.index')}}" class="hint--bottom menu-el @if(\Request::is('*starred*')) current @endif" data-hint="{{trans('documents.menu.recent_hint')}}">
				<span class="menu-icon icon-toggle-black icon-toggle-black-ic_star_black_24dp"></span> {{trans('documents.menu.starred')}}
			</a>
		</li>
		<li>
			<a href="{{route('documents.sharedwithme')}}" data-drop="true" data-drop-action="share" class="menu-el @if(\Request::is('*shared-with-me')) current @endif">
				<span class="menu-icon icon-social-black icon-social-black-ic_people_black_24dp"></span> {{trans('documents.menu.shared')}}
			</a>
		</li>
		<!-- <li>
			<a href="{{route('documents.notindexed')}}" class="menu-el @if(\Request::is('*notindexed')) current @endif">
				<span class="menu-icon icon-action-black icon-action-black-ic_thumb_down_black_24dp"></span> Not indexed
			</a>
		</li> -->
		<li>
			<a href="{{route('documents.trash')}}" data-drop="true" data-drop-action="del" class="menu-el @if(\Request::is('*trash')) current @endif">
				<span class="menu-icon icon-action-black icon-action-black-ic_delete_black_24dp"></span> {{trans('documents.menu.trash')}}
			</a>
		</li>

	</ul>

	@include('groups.tree')
	
</div>

