<div class="panel-cache js-panel-cache">
	
</div>
<div class="panel">

	<a href="#close" title="{{trans('panels.close_btn')}}" class="close icon-navigation-black icon-navigation-black-ic_close_black_24dp"></a>

	<div id="inner">

		{{trans('panels.loading_message')}}

		@yield('panelcontent')

	</div>
</div>

<div class="panel-cache cache--dialog js-dialog-cache">
	
</div>
<div class="dialog js-dialog">

	<a href="#close" title="{{trans('panels.close_btn')}}" class="js-cancel dialog__close close icon-navigation-black icon-navigation-black-ic_close_black_24dp"></a>

	<div class="dialog__content js-dialog-content">
		{{ trans('panels.loading_message') }}
	</div>
</div>
