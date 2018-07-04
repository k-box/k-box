<!doctype html>
<html>
    <body>
    <p>{{ trans('messaging.mail.do_not_reply) }}</p>
    
    {{$user->name}}, your new password is <br/>
    <strong>{{$password}}</strong>
    </body>
</html>
