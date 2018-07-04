<!doctype html>
<html>
    <body>
    
    <p>{{ trans('messaging.mail.do_not_reply) }}</p>

    <div style="display:none">{{ trans('mail.welcome.disclaimer') }}</div>

    <p>
    	{{ trans('mail.welcome.welcome', ['name' => $user['name']]) }}<br/>
    	{!! trans('mail.welcome.credentials', ['mail' => $user['email'], 'password' => $password]) !!}
    </p>

    <p>
    	{!! trans('mail.welcome.login_button', ['link' => \Config::get('app.url')]) !!}
    </p>

    
    </body>
</html>
