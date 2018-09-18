# freshtariffs
Rich OpenSource Telecom Rates Management &amp; API System written in Laravel Framework. for demo check www.freshtarrifs.com .

With FreshTariffs App You Can:

- Add Customers
- Add Price List Types (CLI, NCLI, TDM and others..)
- Send Price List to customers from web interface
- List Customers
- Download Sent Pricelist History
- Generate API key
- Connect to Google Drive and Save Sheet once sent
- Important Customers profiles from FreshBooks API
- Set Price per prefix or country very quick
- Admin Panel (Create Companies, Manage, Create Destinations Database, Add Custom Email Tenmplates and more..)


# Demo Video of the app - Click on Image to watch

[![Audi R8](http://img.youtube.com/vi/g7PhBe46bVI/0.jpg)](https://www.youtube.com/watch?v=g7PhBe46bVI "FreshTariffs Demo")


# Integrations

- Google Drive API
- Stripe For Subscriptions
- Mailgun for sending out emails and tracking delivery.
- WebHooks 
- FreshBooks API 
- REST API 


# Configuration

- Edit app/config/app.php

Set your app URL:


	'url' => 'http://panel.freshtariffs.com',


- Edit app/config/database.php

	'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'Freshtariffs',
			'username'  => 'root',
			'password'  => 'DBPASSWORD',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
		),



- Edit /app/config/services.php

Enter Mailgun domain and secret key to be able to send emails:

	'mailgun' => array(
		'domain' => 'maildelivery.freshtariffs.com',
		'secret' => 'key-12348757575757575775757575',
	),

	'mandrill' => array(
		'secret' => '',
	),
  
  
  Enter Stripe Live Piblic key and Secret to redirect payments to your Stripe Account

	'stripe' => array(
		'model'  => 'User',
        'public' => 'pk_live_fCDbVzSgw434pacbUt7H8ScBg',
		'secret' => 'sk_live_Bswc0037837347474774',
	),

);
