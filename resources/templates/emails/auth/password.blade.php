<!doctype html>
<html>
    <body>

    <h2>K-Link DMS</h2>

    <p>Hi {{$user->name}}. A <strong>password reset request</strong> has been made for your K-Link DMS account.</p>

    <p>If you don't have made the request simply ignore this message.</p>


    <p><a href="{{action('Auth\PasswordController@getReset', [$token])}}">Click here for resetting your password</a></p>

    <p>
    	If the link above will not work copy and paste into your browser address bar the following address:

    	<code>
    		{{action('Auth\PasswordController@getReset', [$token])}}
    	</code>


    </p>

    </body>
</html>

