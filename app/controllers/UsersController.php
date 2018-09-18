<?php
/**
 * File: UsersController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class UsersController extends BaseController {
    public function login() {
        $subdomain = Utils::getSubdomain();
        $companyId = null;
        if (!empty($subdomain)) {
            $company = Company::where('subdomain', Utils::getSubdomain())->first();
            if ($company) {
                $companyId = $company->company_id;
            }
        }

        return View::make('site.users.login', [
            'companyId' => $companyId
        ]);
    }

    public function loginSubmit() {
        $validatorRules = array(
            'email' => 'required|email',
            'password' => 'required|min:6'
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validatorRules);

        if ($validator->fails()) {
            return Redirect::to('/users/login')->with('message', 'Invalid login or password.')->withInput(Input::all());
        }

        $user = User::where('email', Input::get('email'));

        if (Input::has('company_id')) {
            $user->where('company_id', Input::get('company_id'));
        }

        $user = $user->first();
        if (!$user) {
            return Redirect::to('/users/login')->with('message', 'Invalid login or password.')->withInput(Input::all());
        }

        if (!Hash::check(Input::get('password'), $user->password)) {
            return Redirect::to('/users/login')->with('message', 'Invalid login or password.')->withInput(Input::all());
        }

        $user->last_login = \Carbon\Carbon::now();
        $user->save();
        Session::put('user', ['user_id' => $user->user_id]);
        $company = $user->company;
        if ($company && $company->subdomain) {
            return Redirect::to('http://' . Utils::getSubdomainURL($company->subdomain));
        } else {
            return Redirect::to('/');
        }
    }

    public function logout() {
        Session::forget('user');
        return Redirect::to('/users/login')->with('message', 'You have been logged out.');
    }
    
    public function create() {
        return View::make('site.users.create');
    }

    public function createSubmit() {
        $validatorRules = array(
            'company' => 'required|min:4',
            'email' => 'required|email',
            'password' => 'required|min:6'
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validatorRules);

        if ($validator->fails()) {
            return Redirect::to('/users/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $subdomainName = strtolower(preg_replace('/[^a-zA-Z0-9\-_]/', '', Input::get('company')));
        $subdomainCounter = 0;
        // if company with this subdomain already exists
        if (Company::where('subdomain', $subdomainName)->count() !== 0) {
            // will trying to create subdomain like "{$name}0", "{$name}1", ...
            // until don't find not used
            while (Company::where('subdomain', $subdomainName . $subdomainCounter)->count() !== 0) {
                $subdomainCounter++;
            }
            $subdomainName = $subdomainName . $subdomainCounter;
        }
        
        $company = new Company;
        $company->name = Input::get('company');
        $company->subdomain = $subdomainName;
        $company->email = Input::get('email');
        $company->email_reply_to = Input::get('email');
        $company->email_subject = Settings::getOption('default_email_subject');
        $company->email_template = Settings::getOption('default_email_template');
        $company->save();

        /** @var Company $company */
        $company = Company::where('subdomain', $subdomainName)->first();
        $user = new User;
        $user->company_id = $company->company_id;
        $user->username = Input::get('company');
        $user->email = Input::get('email');
        $user->password = Hash::make(Input::get('password'));
        $user->role = User::ROLE_COMPANY;
        $user->last_login = \Carbon\Carbon::now();
        $user->save();

        return Redirect::to('http://' . Utils::getSubdomainURL($company->subdomain));
    }

    public function settings() {
        
    }
}