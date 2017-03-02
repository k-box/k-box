<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Mail messages
	|--------------------------------------------------------------------------
	|
	|
	*/

	// Global layout elements
	'logo_text' => 'K-Link DMS',
	'footer_disclaimer' => "You're receiving this email because of your account on <a href=\":url\">:instance</a>",
	'footer_help' => "<a href=\":url\">Help</a>",

	'welcome' => array(

		/*
			Welcome mail template
		 */

		'disclaimer' => 'This E-Mail contains your access credentials, please keep it in a safe place.',
		'welcome' => 'Welcome :name,',
		'credentials' => 'you can now access the K-Link DMS of your Institution with<br/>username <strong>:mail</strong><br/>password <strong>:password</strong>',

		'login_button' => '<a href=":link">Login</a>',
		

	),
    
    'password_reset_subject' => 'You requested a password reset for your DMS account',



	'sharecreated' => [
		/**
		 * Strings for the mails.shares.created email template. Used when a share to a document is created
		 */
		'subject' => 'K-Link DMS - :user has shared :title with you',
		'shared_document_with_you' => ':user shared a document with you',
		'shared_collection_with_you' => ':user shared a collection with you',
		'title_label' => 'Title',
	],


	
];
