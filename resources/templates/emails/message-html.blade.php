<!doctype html>
<html>
    <body>

    <img src="<?php echo $message->embed(public_path('images/klink_mail_logo.png')); ?>" alt="K-Link">

    <p>{{ trans('messaging.mail.intro', ['name' => $user['name']]) }}</p>

    <p>{{ $text }}</p>

    <p>
        {!! trans('messaging.mail.signature', ['name' => $sender]) !!}
    </p>
    
    
    <p>
    	{!! trans('messaging.mail.you_are_receiving_because', ['link' => \Config::get('app.url')]) !!}
    </p>
    
    </body>
</html>