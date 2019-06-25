@if(support_token() !== null)
	
	<script>

		UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/{{ support_token() }}.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

		UserVoice.push(['set', {
		  accent_color: '#448dd6',
		  trigger_color: 'white',
          locale: '{{ \App::getLocale() }}',
		  trigger_background_color: '#448dd6',
		  ticket_custom_fields: {			
			'context': '{"product": "{{$product}}","version": "{{$version}}","route": "{{$route}}","context": "{{$context}}","group": "{{$group}}","visibility": "{{$visibility}}","search_terms" => "{{$search_terms}}"}'
		  },
		}]);
		
		@if(isset($feedback_loggedin) && $feedback_loggedin)
            UserVoice.push(['identify', {
                email: '{{$feedback_user_mail}}',
                name: '{{$feedback_user_name}}',
            }]);
		@endif
		
		UserVoice.push(['addTrigger', { mode: 'contact', trigger_position: 'bottom-right' }]);
		UserVoice.push(['autoprompt', {}]);
	</script>
    
@endif