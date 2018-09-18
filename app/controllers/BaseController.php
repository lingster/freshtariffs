<?php

class BaseController extends Controller {
    /* @var User */
    protected static $user;

    /**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}

        View::share('isLoggedIn', self::isLoggedIn());
        View::share('user', self::getCurrentUser());
    }

    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return NULL;
        }

        $user = Session::get('user', NULL);
        if (self::$user == null) {
            $user = User::where('user_id', $user['user_id'])->first();
            self::$user = $user;
        }

        return self::$user;
    }

    public static function isLoggedIn() {
        $user = Session::get('user', NULL);
        if ($user !== NULL) {
            return true;
        } else {
            return false;
        }
    }

    public static function isAdmin() {
        if (!self::isLoggedIn()) {
            return false;
        }

        $user = self::getCurrentUser();
        return $user->role == User::ROLE_ADMIN;
    }

    public static function isStaff() {
        if (!self::isLoggedIn()) {
            return false;
        }

        $user = self::getCurrentUser();
        return $user->role == User::ROLE_COMPANY;
    }

    public static function isCustomer() {
        if (!self::isLoggedIn()) {
            return false;
        }

        $user = self::getCurrentUser();
        return $user->role == User::ROLE_CUSTOMER;
    }

    public static function isSubscribed() {
        if (!self::isLoggedIn()) {
            return false;
        }

        $user = self::getCurrentUser();
        return (bool) $user->subscribed();
    }

}
