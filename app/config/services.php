<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Third Party Services
	|--------------------------------------------------------------------------
	|
	| This file is for storing the credentials for third party services such
	| as Stripe, Mailgun, Mandrill, and others. This file provides a sane
	| default location for this type of information, allowing packages
	| to have a conventional place to find your various credentials.
	|
	*/

	'mailgun' => array(
		'domain' => 'maildelivery.freshtariffs.com',
		'secret' => 'key-c9818d3b9705de4b8c66528732d5a8ab',
	),

	'mandrill' => array(
		'secret' => '',
	),

	'stripe' => array(
		'model'  => 'User',
        'public' => 'pk_live_fCDbVzSgwAxpacbUt7H8ScBg',
		'secret' => 'sk_live_BswcOO23c5lK4i16orH5UWNa',
	),

);
