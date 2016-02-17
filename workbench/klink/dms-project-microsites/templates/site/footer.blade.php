
<div class="footer">
    <div class="inner">
        &copy; {{ date('Y') }}.
    </div>
</div>


<script>
		
		<?php 
		
		$support_context = json_encode(array(
			'product' => 'DMS Project',
			'version' => \Config::get("dms.version"),
			'route' => !is_null(\Route::getCurrentRoute()->getName()) ? \Route::getCurrentRoute()->getName() : \Route::getCurrentRoute()->getPath(),
			'context' => isset($context) ? $context : null,
			'group' => isset($context_group) ? $context_group : null,
			'visibility' => isset($current_visibility) ? $current_visibility : null,
			'search_terms' => isset($search_terms) ? e($search_terms) : null,
		));
		
		?>
		
		UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/{{\Config::get("dms.feedback_api_key")}}.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

		UserVoice.push(['set', {
		  accent_color: '#448dd6',
		  trigger_color: 'white',
		  trigger_background_color: '#448dd6',
		  ticket_custom_fields: {
			
			'context': '{!!$support_context!!}'
			
		  },
		}]);
		
		@if(isset($feedback_loggedin) && $feedback_loggedin)
		UserVoice.push(['identify', {
		  email:      '{{$feedback_user_mail}}',
		  name:       '{{$feedback_user_name}}',
		}]);
		
		@endif
		
		UserVoice.push(['addTrigger', { mode: 'contact', trigger_position: 'bottom-right' }]);
		UserVoice.push(['addTrigger', '#support_trigger', { }]);
		UserVoice.push(['autoprompt', {}]);
	</script>