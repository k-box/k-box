<!doctype html>
<html>
    <body>

    <img src="{{ $message->embed(public_path('images/klink_mail_logo.png')) }}" alt="K-Link">
    
    <p>{{ trans('messaging.mail.intro', ['name' => $user['name']]) }}</p>

    {!! $text !!}

    <p>
        {!! trans('messaging.mail.signature', ['name' => $sender]) !!}
    </p>
    
    
    <p>
    	{!! trans('messaging.mail.you_are_receiving_because', ['link' => \Config::get('app.url')]) !!}
    </p>
    
    <p>{{ trans('messaging.mail.do_not_reply) }}</p>

    
    </body>
</html>
