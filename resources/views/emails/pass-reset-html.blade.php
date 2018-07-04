<!doctype html>
<html>
    <body>    
    {{$user->name}}, your new password is <br/>
    <strong>{{$password}}</strong>
    
   <p>{{ trans('messaging.mail.do_not_reply) }}</p>

    </body>
</html>
