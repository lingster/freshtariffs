<?php
/**
 * File: CustomerController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

use \Chrisbjr\ApiGuard\Controllers\ApiGuardController;

class API_v1_CustomerController extends ApiGuardController {
    public function all() {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $users = $company->users()->where('role', User::ROLE_CUSTOMER)->paginate(100);
        return $this->response->withPaginator($users, new UserTransformer, 'customers');
    }

    public function show($userId) {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $user = $company->users()->where('role', User::ROLE_CUSTOMER)->where('user_id', $userId)->first();
        if (!$user) {
            return $this->response->errorNotFound();
        }
        return $this->response->withItem($user, new UserTransformer, 'customer');
    }

    public function update($userId) {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $user = $company->users()->where('role', User::ROLE_CUSTOMER)->where('user_id', $userId)->first();
        if (!$user) {
            return $this->response->errorNotFound();
        }

        $validationRules = array(
            'username' => 'sometimes|required|min:1',
            'emails.primary' => 'sometimes|required|email',
            'emails.cc' => 'sometimes|required|email',
            'emails.bcc' => 'sometimes|required|email',
        );

        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return $this->response->errorWrongArgsValidator($validator);
        }

        if (User::where('company_id', $company->company_id)->where('email', Input::get('emails.primary'))->count() > 0) {
            $validator->messages()->add('email', 'The email has already been taken.');
            return $this->response->errorWrongArgsValidator($validator);
        }

        $user->username = Input::get('username', $user->username);
        $user->email = Input::get('emails.primary', $user->email);
        $user->email_cc = Input::get('emails.cc', $user->email_cc);
        $user->email_bcc = Input::get('emails.bcc', $user->email_bcc);
        $user->save();
        return Response::json([
            'success' => [
                'code' => 'OK',
                'http_code' => 200,
                'message' => 'OK'
            ]
        ]);
    }

    public function delete($userId) {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        /** @var User $user */
        $user = $company->users()->where('role', User::ROLE_CUSTOMER)->where('user_id', $userId)->first();
        if (!$user) {
            return $this->response->errorNotFound();
        }

        $user->deleteWithCleanup();
        return Response::json([
            'success' => [
                'code' => 'OK',
                'http_code' => 200,
                'message' => 'OK'
            ]
        ]);
    }


    public function create() {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $validationRules = array(
            'username' => 'required',
            'emails.primary' => 'required|email',
            'emails.cc' => 'sometimes|required|email',
            'emails.bcc' => 'sometimes|required|email',
            'password' => 'required|min:6',
        );

        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return $this->response->errorWrongArgsValidator($validator);
        }

        if (User::where('company_id', $company->company_id)->where('email', Input::get('emails.primary'))->count() > 0) {
            $validator->messages()->add('email', 'The email has already been taken.');
            return $this->response->errorWrongArgsValidator($validator);
        }

        $user = new User;
        $user->company_id = $company->company_id;
        $user->username = Input::get('username');
        $user->email = Input::get('emails.primary');
        $user->email_cc = Input::get('emails.cc');
        $user->email_bcc = Input::get('emails.bcc');
        $user->password = Hash::make(Input::get('password'));
        $user->role = User::ROLE_CUSTOMER;
        $user->save();
        $user->sendRegisteredEmail();
        return Response::json([
            'success' => [
                'code' => 'OK',
                'http_code' => 200,
                'message' => 'OK'
            ]
        ]);
    }
}