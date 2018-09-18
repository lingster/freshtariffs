<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
        if (self::$user->role === User::ROLE_ADMIN) {
            return Redirect::to('/admin');
        }

        if (self::$user->role === User::ROLE_COMPANY) {
            return Redirect::to('/staff/dashboard');
        }

        if (self::$user->role === User::ROLE_CUSTOMER) {
            return Redirect::to('/customer/dashboard');
        }
	}

    public function test() {
        $destinations = Destination::whereNull('country')->get();
        foreach ($destinations as $destination) {
            $title = $destination->network_name;
            list($country, $network_name) = explode('-', $title, 2);
            $destination->country = $country;
            $destination->network_name = $network_name;
            $destination->save();
        }
    }
}
