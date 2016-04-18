<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Password Reminder Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are the default lines which match reasons
	| that are given by the password broker for a password update attempt
	| has failed, such as for an invalid token or invalid new password.
	|
	*/

	"password" => "Passwords must be at least six characters and match the confirmation.",
	"user" => "We can't find a user with that e-mail address.",
	"token" => "The password reset link you have used is expired. It will be usable only for 5 minutes after the password reset request.",
	"sent" => "Password reset link sent!",
	"reset" => "Password has been reset!",

	'forgot' => array(

		'link' => 'Forgot your password?',

		'title' => 'Forgot your password?',

		'instructions' => 'To reset your password please specify your E-Mail address. A mail with a reset link will be sended to your E-Mail account.',

		'submit' => 'Request a Password Reset',

		'email_subject' => 'K-Link DMS Password Reset Request',

	),


	'reset' => array(

		// 'link' => 'Forgot your password?',

		'title' => 'Reset your account password',

		'instructions' => 'Please specify a new 8 character long alphanumeric password.',

		'submit' => 'Reset the Password',

		'email_subject' => 'K-Link DMS Your Account Password has been changed',

	),

	



];
