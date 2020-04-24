


<div class="c-panel js-panel">
	
	<div class="c-panel__cache close js-panel-close">
	</div>


	<div class="c-panel__content">
		<a href="#close" title="{{trans('panels.close_btn')}}" class="button c-panel__close p-2 js-panel-close">@materialicon('navigation', 'close')</a>

		<div class="js-panel-content" style="height:100%">
			{{trans('panels.loading_message')}}

			@yield('panelcontent')
		</div>

	</div>
</div>


<div class="c-dialog js-dialog">

	<div class="c-dialog__cache js-cancel">
	</div>

	<div class="c-dialog__content">
		<a href="#close" title="{{trans('panels.close_btn')}}" class="button button--ghost js-cancel c-dialog__close">@materialicon('navigation', 'close')</a>

		<div class="js-dialog-content">
			{{ trans('panels.loading_message') }}
		</div>
	</div>

</div>

