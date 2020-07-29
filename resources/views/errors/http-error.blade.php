<html>
	<head lang="{{ app()->getLocale() }}">


		<title>@yield('title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

		<style>


			body {
				background: #283593;

				font-family: 'Nunito', sans-serif;

				color: #fff;

				padding: 40px;
			}

			.title {
				font-size: 40px;
				margin-top: 28px;
			}

			.dms_tag {
				font-size: 18px;
				margin: 14px 0;
			}
            
            .button {
                padding:12px;
                background-color:#fff;
                color: #283593;
            }

			.logo {
				width: 48px;
				height: 48px;
                fill: #fff;
			}
            .logo svg {
				width: 48px;
				height: 48px;
            }

		</style>
		<link href='//fonts.googleapis.com/css?family=Nunito:300,700' rel='stylesheet' type='text/css'>
		
	</head>
	<body>
		<div class="container">
			<div class="content">

				<x-logo class="text-white" />

				<div class="dms_tag">{{config('app.name')}}</div>

				<div class="title">@yield('content')</div>
        
                @if(support_token() !== false)        
                <p>
                    {{ trans('errors.support_widget_opened_for_you') }}
                </p>
                @endif
                <p>&nbsp;</p>

                @section('actions')
                <div>
                    <a class="button" href="{{ redirect()->back()->getTargetUrl() }}">{{ trans('errors.go_back_btn') }}</a>
                </div>
                @show
			</div>
		</div>
        
        

        
        @if(support_token() !== false)
	
        <script>
            
            <?php 
            
            $support_context = json_encode([
                'product' => 'DMS Project',
                'version' => \Config::get("dms.version"),
                'route' => ! is_null(\Route::getCurrentRoute()) ? \Route::getCurrentRoute()->getName() : null,
                'context' => isset($context) ? $context : null,
                'lang' => app()->getLocale(),
                'reason' => isset($reason) ? e($reason) : null,
            ]);
            
            ?>
            
            UserVoice=window.UserVoice||[];(function(){var uv=document.createElement('script');uv.type='text/javascript';uv.async=true;uv.src='//widget.uservoice.com/{{ support_token() }}.js';var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(uv,s)})();

            UserVoice.push(['set', {
            accent_color: '#448dd6',
            trigger_color: 'white',
            locale: '{{ \App::getLocale() }}',
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
            UserVoice.push(['show']);
        </script>
        
        @endif
        
        
	</body>
</html>
