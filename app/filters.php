<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

App::missing(function($request)
{
    if (Request::is('api/v1/*')) {
        return Response::json([
            'error' => [
                'code' => "GEN-NOT-FOUND",
                'http_code' => 404,
                'message' => "Resource Not Found"
            ]
        ]);
    }

    View::share('isLoggedIn', BaseController::isLoggedIn());
    View::share('user', BaseController::getCurrentUser());
    return View::make('errors.404');
});

App::error(function(\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $exception)
{
    if (Request::is('api/v1/*')) {
        return Response::json([
            'error' => [
                'code' => "GEN-METHOD-NOT-ALLOWED",
                'http_code' => 405,
                'message' => "Method Not Allowed"
            ]
        ]);
    }
});
/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/


Route::filter('auth', function()
{
	if (!BaseController::isLoggedIn())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::to('/users/login')->with('message', 'You must be logged in.');
		}
	}
});

Route::filter('admin', function() {
    if (!BaseController::isAdmin()) {
        if (Request::ajax())
        {
            return Response::make('Unauthorized', 401);
        }
        else
        {
            App::abort(404);
        }
    }
});

Route::filter('staff', function() {
    if (!BaseController::isStaff()) {
        if (Request::ajax())
        {
            return Response::make('Unauthorized', 401);
        }
        else
        {
            App::abort(404);
        }
    }

    $user = BaseController::getCurrentUser();
    $company = $user->company;
    if ($company && $company->subdomain) {
        $httpHost = Utils::getSubdomainURL($company->subdomain);
        if ($httpHost != $_SERVER['HTTP_HOST']) {
            return Redirect::to('http://' . $httpHost . $_SERVER['REQUEST_URI']);
        }
    }
});

Route::filter('customer', function() {
    if (!BaseController::isCustomer()) {
        if (Request::ajax())
        {
            return Response::make('Unauthorized', 401);
        }
        else
        {
            App::abort(404);
        }
    }

    $user = BaseController::getCurrentUser();
    $company = $user->company;
    if ($company && $company->subdomain) {
        $httpHost = Utils::getSubdomainURL($company->subdomain);
        if ($httpHost != $_SERVER['HTTP_HOST']) {
            return Redirect::to('http://' . $httpHost . $_SERVER['REQUEST_URI']);
        }
    }
});

Route::filter('subscribed', function() {
    if (!BaseController::isSubscribed()) {
        if (Request::ajax())
        {
            return Response::make('Unauthorized', 401);
        }
        else
        {
            App::abort(404);
        }
    }
});

Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (BaseController::isLoggedIn()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
