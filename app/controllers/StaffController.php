<?php
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;

/**
 * File: StaffController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class StaffController extends BaseController {
    public function dashboard() {
        /** @var Company $company */
        $company = Company::where('company_id', self::$user->company->company_id)->first();
        if (!$company) {
            App::abort(404);
        }
        $customersCount = $company->users()->where('role', User::ROLE_CUSTOMER)->count();
        $pricelistsCount = PricelistInfo::where('company_id', self::$user->company->company_id)->count();
        return View::make('site.staff.dashboard', [
            'customersCount' => $customersCount,
            'pricelistsCount' => $pricelistsCount
        ]);
    }

    public function customers() {
        /** @var Company $company */
        $company = Company::where('company_id', self::$user->company->company_id)->first();
        if (!$company) {
            App::abort(404);
        }

        $staffOnly = Input::has('staff');
        $companyUsers = $company->users()->where('role', $staffOnly ? User::ROLE_COMPANY : User::ROLE_CUSTOMER)->orderBy('user_id', 'asc')->get();
        return View::make($staffOnly ? 'site.staff.customers_staff' : 'site.staff.customers', ['users' => $companyUsers, 'company' => $company]);
    }

    public function customersCreate() {
        /** @var Company $company */
        $company = Company::where('company_id', self::$user->company->company_id)->first();
        if (!$company) {
            App::abort(404);
        }
        return View::make('site.staff.customers_create', ['company' => $company]);
    }

    public function customersCreateSubmit() {
        /** @var Company $company */
        $companyId = self::$user->company->company_id;
        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            App::abort(404);
        }

        $validationRules = array(
            'username' => 'required',
            'email' => 'required|email',
            'email_cc' => 'email',
            'email_bcc' => 'email',
            'password' => 'required|min:6',
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::to('/staff/customers/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $email = Input::get('email');
        if (User::where('company_id', $companyId)->where('email', $email)->count() > 0) {
            $validator->messages()->add('email', 'The email has already been taken.');
            return Redirect::to('/staff/customers/create')->withErrors($validator->errors())->withInput(Input::all());
        }


        if (!\Restrictions::hasAccess(self::$user, \Restrictions::RESTRICTION_CUSTOMER_COUNT)) {
            $validator->messages()->add('company_id', 'You exceeded limit for your subscription plan. Visit subscription page for more information.');
            return Redirect::to('/staff/customers/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $user = new User;
        $user->company_id = $companyId;
        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->email_cc = Input::get('email_cc');
        $user->email_bcc = Input::get('email_bcc');
        $user->password = Hash::make(Input::get('password'));
        $user->role = User::ROLE_CUSTOMER;
        $user->save();
        $user->sendRegisteredEmail();

        return Redirect::to('/staff/customers')->with('message', 'Customer created.');
    }

    public function customersDelete($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }

        if ($userId == self::$user['user_id']) {
            App::abort(500);
        }

        $user->deleteWithCleanup();
        return Redirect::to('/staff/customers')->with('message', 'Customer deleted.');
    }

    public function customersEdit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }

        $company = $user->company;
        return View::make('site.staff.customers_edit', [
            'company' => $company,
            'selectedUser' => $user
        ]);
    }

    public function customersEditSubmit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }

        $validationRules = array(
            'username' => 'required',
            'email' => 'required|email',
            'email_cc' => 'email',
            'email_bcc' => 'email',
            'password' => 'min:3',
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::to('/staff/customers/' . $user->user_id . '/edit')->withErrors($validator->errors())->withInput(Input::all());
        }

        $email = Input::get('email');
        if ($email != $user->email && User::where('company_id', $user->company_id)->where('email', $email)->count() > 0) {
            $validator->messages()->add('email', 'The email has already been taken.');
            return Redirect::to('/staff/customers/' . $user->user_id . '/edit')->withErrors($validator->errors())->withInput(Input::all());
        }

        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->email_cc = Input::get('email_cc');
        $user->email_bcc = Input::get('email_bcc');
        if (!empty(Input::get('password'))) {
            $user->password = Hash::make(Input::get('password'));
        }

        $user->save();
        return Redirect::to('/staff/customers')->with('message', 'User edited.');
    }

    public function settings() {
        /** @var Company $company */
        $company = Company::where('company_id', self::$user->company->company_id)->first();
        if (!$company) {
            App::abort(404);
        }

        return View::make('site.staff.settings', ['company' => $company]);
    }

    public function settingsSubmit() {
        /** @var Company $company */
        $company = Company::where('company_id', self::$user->company->company_id)->first();
        if (!$company) {
            App::abort(404);
        }

        $validatorRules = array(
            'name' => 'required',
            'email' => 'required|email',
            'address' => 'min:10',
            'website' => 'url',
            'email_template' => 'required|min:10',
            'email_subject' => 'required|min:5',
            'email_reply_to' => 'required|email',
            'date_format' => 'required|in:"' . implode('","', array_keys(Company::getDateFormats())) . '"'
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validatorRules);

        if ($validator->fails()) {
            return Redirect::to('/staff/settings')->withErrors($validator->errors())->withInput(Input::all());
        }

        $company->name = Input::get('name');
        $company->email = Input::get('email');
        $company->address = Input::get('address');
        $company->phone = Input::get('phone');
        $company->website = Input::get('website');
        $company->email_template = Input::get('email_template');
        $company->email_subject = Input::get('email_subject');
        $company->email_reply_to = Input::get('email_reply_to');
        $company->date_format = Input::get('date_format');
        $company->save();

        return Redirect::to('/staff/settings')->with('message', 'Company edited');
    }

    public function pricelists() {
        $pricelists = PricelistInfo::where('company_id', self::$user->company->company_id)->orderBy('id', 'desc')->paginate(20);
        return View::make('site.staff.pricelists', ['pricelists' => $pricelists]);
    }

    public function pricelist($userId = null) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }

        $company = $user->company;
        $companyUsers = $company->users;
        $companyUsersSelect = [];
        foreach ($companyUsers as $companyUser) {
            $companyUsersSelect[$companyUser->user_id] = $companyUser->username;
        }

        /*
        $latestPricelistId = Pricelist::getLatestPricelistId($userId);
        $latestPricelistValues = [];
        if ($latestPricelistId) {
            $latestPricelist = Pricelist::where('price_id', $latestPricelistId)->get();
            foreach ($latestPricelist as $record) {
                $latestPricelistValues[$record->destination_id] = $record->rate;
            }
        }*/

        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);

        return View::make('site.staff.pricelist', [
            'selectedUser' => $user,
            //'latestPricelistValues' => $latestPricelistValues,
            'companyUsers' => $companyUsers,
            'companyUsersSelect' => $companyUsersSelect,
            'pricelistTypesSelect' => $pricelistTypesSelect
        ]);
    }

    public function pricelistSubmit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }
        /** @var Company $company */
        $company = self::$user->company;
        $validationRules = array();
        $validationRules['users'] = 'required|array';
        $validationRules['effective_date'] = 'required|after:yesterday';

        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);
        $pricelistTypesValidation = array_values($pricelistTypesSelect);
        $validationRules['type'] = 'required|in:' . implode(',', $pricelistTypesValidation);

        $pricelistData = Input::except(['_token', 'users', 'type', 'effective_date']);
        $result = Pricelist::buildPricelistFromPostData($pricelistData);
        $pricelist = $result['pricelist'];

        $validationRules = array_merge($result['validation'], $validationRules);
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::to('/staff/pricelist/' . $userId)->withErrors($validator->errors())->withInput(Input::all())->with('pricelist', $pricelist);
        }


        if (count($pricelist) < 1) {
            $validator->messages()->add('rate', 'At least one destination must be selected.');
            return Redirect::to('/staff/pricelist/' . $userId)->withErrors($validator->errors())->withInput(Input::all())->with('pricelist', $pricelist);
        }

        $companyUsers = $company->users;
        $companyUsersValidation = [];
        foreach ($companyUsers as $user) {
            $companyUsersValidation[] = $user->user_id;
        }

        $users = Input::get('users');
        foreach ($users as $selectedUserId) {
            if(!in_array($selectedUserId, $companyUsersValidation)) {
                $validator->messages()->add('users', 'Invalid user selected.');
                return Redirect::to('/staff/pricelist/' . $userId)->withErrors($validator->errors())->withInput(Input::all())->with('pricelist', $pricelist);
            }
        }

        foreach ($users as $selectedUserId) {
            $pricelistInfo = Pricelist::storePricelist($selectedUserId, $pricelist);
            $company->generateAndSendEmail($selectedUserId);
            Integrations::GoogleDriveStorePricelist($company->company_id, $selectedUserId, $pricelistInfo);
            Integrations::WebhookStorePricelist($company->company_id, $selectedUserId, $pricelistInfo);
        }

        return Redirect::to('/staff/customers')->with('message', 'Price list created.');
    }

    public function pricelistXls($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }

        $company = self::$user->company;
        $companyUsers = $company->users;
        $companyUsersSelect = [];
        foreach ($companyUsers as $companyUser) {
            $companyUsersSelect[$companyUser->user_id] = $companyUser->username;
        }

        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);

        return View::make('site.staff.pricelist_xls', [
            'selectedUser' => $user,
            'companyUsers' => $companyUsers,
            'companyUsersSelect' => $companyUsersSelect,
            'pricelistTypesSelect' => $pricelistTypesSelect
        ]);
    }

    public function pricelistXlsSubmit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->where('company_id', self::$user->company->company_id)->first();
        if (!$user) {
            App::abort(404);
        }

        $company = self::$user->company;
        $validationRules = array(
            'users' => 'required|array',
            'file' => 'required|mimes:xlsx',
            'effective_date' => 'required|after:yesterday'
        );

        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);
        $pricelistTypesValidation = array_values($pricelistTypesSelect);
        $validationRules['type'] = 'required|in:' . implode(',', $pricelistTypesValidation);

        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return Redirect::to('/staff/pricelist/' . $userId . '/xls')->withErrors($validator->errors())->withInput(Input::all());
        }

        $file = Input::file('file');
        $filename = Str::random() . '.xlsx';
        if (!$file || !$file->isValid()) {
            $validator->messages()->add('file', 'Uploaded file is not valid.');
            return Redirect::to('/staff/pricelist/' . $userId . '/xls')->withErrors($validator->errors())->withInput(Input::all());
        }
        $file->move(storage_path() . '/imports/', $filename);

        $companyUsers = $company->users;
        $companyUsersValidation = [];
        foreach ($companyUsers as $user) {
            $companyUsersValidation[] = $user->user_id;
        }

        $users = Input::get('users');
        foreach ($users as $selectedUserId) {
            if(!in_array($selectedUserId, $companyUsersValidation)) {
                $validator->messages()->add('users', 'Invalid user selected.');
                return Redirect::to('/staff/pricelist/' . $userId . '/xls')->withErrors($validator->errors())->withInput(Input::all());
            }
        }

        $postData = [];
        $workbook = SpreadsheetParser::open(storage_path() . '/imports/' . $filename);
        foreach ($workbook->createRowIterator(0) as $rowIndex => $values) {
            if ($rowIndex < 8) continue; // template header
            if (count($values) < 5) continue; // rate is not filled
            list($destinationId, , , , $rate) = $values;
            if (!is_numeric($rate)) {
                continue;
            }

            $postData['rate_' . $destinationId] = $rate;
        }

        $result = Pricelist::buildPricelistFromPostData($postData);
        $pricelist = $result['pricelist'];

        foreach ($users as $selectedUserId) {
            $pricelistInfo = Pricelist::storePricelist($selectedUserId, $pricelist);
            $company->generateAndSendEmail($selectedUserId);
            Integrations::GoogleDriveStorePricelist($company->company_id, $selectedUserId, $pricelistInfo);
            Integrations::WebhookStorePricelist($company->company_id, $selectedUserId, $pricelistInfo);
        }

        return Redirect::to('/staff/customers')->with('message', 'Price list created.');
    }

    public function subscription() {
        return View::make('site.staff.subscription');
    }

    public function subscriptionCancel() {
        self::$user->subscription()->cancel();
        return Redirect::to('/staff/subscription')->with('message', 'Subscription cancelled.');
    }
    
    public function subscriptionInvoices() {
        if (self::$user->everSubscribed()) {
            $invoices = self::$user->invoices();
        } else {
            $invoices = [];
        }
        return View::make('site.staff.subscription_invoices', [
            'invoices' => $invoices
        ]);
    }
    
    public function subscriptionInvoicesDownload($invoiceId) {
        return self::$user->downloadInvoice($invoiceId, [
            'vendor' => 'TelecomsXChange',
            'product' => 'Fresh Tariffs'
        ]);
    }
    
    public function integrations() {
        $integrationServices = IntegrationService::all();
        return View::make('site.staff.settings_integrations', [
            'services' => $integrationServices
        ]);
    }

    public function integrationsConnect($serviceId) {
        /** @var IntegrationService $service */
        $service = IntegrationService::where('service_id', $serviceId)->first();
        if (!$service || $service->isIntegrated(self::$user->company->company_id)) {
            App::abort(404);
        }

        if ($serviceId == IntegrationService::SERVICE_GOOGLE_DRIVE) {
            $google = Google::getClient();
            return Redirect::to($google->createAuthUrl());
        }

        if ($serviceId == IntegrationService::SERVICE_WEBHOOK) {
            if (Input::has('webhook_url')) {
                $validationRules = array(
                    'webhook_url' => 'url'
                );

                $validator = Validator::make(Input::all(), $validationRules);
                if ($validator->fails()) {
                    return Redirect::to('/staff/settings/integrations/connect/' . $serviceId . '?_token=' . csrf_token())->withErrors($validator->errors())->withInput(Input::all());
                }
                $integration = new Integration;
                $integration->service_id = IntegrationService::SERVICE_WEBHOOK;
                $integration->company_id = self::$user->company->company_id;
                $integration->token = Input::get('webhook_url');
                $integration->account = Input::get('webhook_url');
                $integration->save();
                return Redirect::to('/staff/settings/integrations')->with('message', 'Service connected.');
            }

            return View::make('site.staff.settings_integrations_webhook');
        }

        if ($serviceId == IntegrationService::SERVICE_API) {
            $apiKey = new \Chrisbjr\ApiGuard\Models\ApiKey;
            $apiKey->user_id = self::$user->company->company_id;
            $apiKey->key = $apiKey->generateKey();
            $apiKey->level = Config::get('panel.api_default_key_level');
            $apiKey->ignore_limits = 0;
            $apiKey->save();

            $integration = new Integration;
            $integration->service_id = IntegrationService::SERVICE_API;
            $integration->company_id = self::$user->company->company_id;
            $integration->token = $apiKey->key;
            $integration->account = $apiKey->key;
            $integration->save();

            return Redirect::to('/staff/settings/integrations')->with('message', 'Service connected.');
        }

        if ($serviceId == IntegrationService::SERVICE_FRESHBOOKS) {
            if (Input::has('url')) {
                $validationRules = array(
                    'url' => 'required|url|regex:/^http[s]?:\/\/([a-z0-9\-_]+?)\.freshbooks\.com.*/i',
                    'token' => 'required|alpha_num'
                );

                $validator = Validator::make(Input::all(), $validationRules);
                if ($validator->fails()) {
                    return Redirect::to('/staff/settings/integrations/connect/' . $serviceId . '?_token=' . csrf_token())->withErrors($validator->errors())->withInput(Input::all());
                }

                preg_match('/^http[s]?:\/\/([a-z0-9\-_]+?)\.freshbooks\.com.*$/i', strtolower(Input::get('url')), $domain);
                if (!isset($domain[1])) {
                    return Redirect::to('/staff/settings/integrations/connect/' . $serviceId . '?_token=' . csrf_token())->withInput(Input::all())->with('error', 'There was an error with integration. Please check URL and authentication token.');
                }

                $domain = $domain[1];
                /* OAuth
                Session::put('integrations_freshbooks_domain', $domain);
                $freshbooks = new Freshbooks(
                    Config::get('panel.freshbooks_oauth_consumer_key'),
                    Config::get('panel.freshbooks_oauth_consumer_secret'),
                    URL::to('/staff/integrations/callback/' . $serviceId),
                    $domain
                );
                return Redirect::to($freshbooks->getLoginUrl());
                */

                $apiURL = sprintf('https://%s.freshbooks.com/api/2.1/xml-in', $domain);
                $token = Input::get('token');
                $request = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<request method="system.current">
</request>
XML;
                try {
                    $result = Freshbooks::tokenPost($apiURL, $token, $request);
                    $companyName = (string) $result->system->company_name;
                    if (empty($companyName)) {
                        throw new Exception('Fresh Books integration: company name is empty.');
                    }

                    $integration = new Integration;
                    $integration->service_id = IntegrationService::SERVICE_FRESHBOOKS;
                    $integration->company_id = self::$user->company->company_id;
                    $integration->token = json_encode([
                        'access_token' => $token,
                        'domain' => $domain
                    ]);
                    $integration->account = $companyName;
                    $integration->save();
                    return Redirect::to('/staff/settings/integrations')->with('message', 'Service connected.');
                } catch (Exception $e) {
                    return Redirect::to('/staff/settings/integrations/connect/' . $serviceId . '?_token=' . csrf_token())->withInput(Input::all())->with('error', 'There was an error with integration. Please check URL and authentication token.');
                }
            }

            return View::make('site.staff.settings_integrations_freshbooks');
        }

        App::abort(404);
    }

    public function integrationsDisconnect($serviceId) {
        /** @var IntegrationService $service */
        $service = IntegrationService::where('service_id', $serviceId)->first();
        if (!$service || !$service->isIntegrated(self::$user->company->company_id)) {
            App::abort(404);
        }

        if ($serviceId == IntegrationService::SERVICE_API) {
            $apiKey = \Chrisbjr\ApiGuard\Models\ApiKey::where('user_id', self::$user->company->company_id)->first();
            \Chrisbjr\ApiGuard\Models\ApiLog::where('api_key_id', $apiKey->id)->delete();
            $apiKey->delete();
        }

        $integration = $service->getIntegration(self::$user->company->company_id);
        $integration->delete();
        return Redirect::to('/staff/settings/integrations')->with('message', 'Service disconnected.');
    }

    public function integrationsCallback($serviceId) {
        /** @var IntegrationService $service */
        $service = IntegrationService::where('service_id', $serviceId)->first();
        if (!$service || $service->isIntegrated(self::$user->company->company_id)) {
            App::abort(404);
        }

        if ($serviceId == IntegrationService::SERVICE_GOOGLE_DRIVE) {
            $code = Input::get('code', false);
            if (!$code) {
                return Redirect::to('/staff/settings/integrations')->with('error', 'There was an error with integration (#1).');
            }

            try {
                $google = Google::getClient();
                $token = $google->authenticate($code);
                $google->setAccessToken($token);
                /** @var Google_Service_Oauth2 $oAuthClient */
                $oAuthClient = Google::make('oauth2');
                $oAuthClient->getClient()->setAccessToken($token);
                $userInfo = $oAuthClient->userinfo->get();
            } catch(Exception $e) {
                return Redirect::to('/staff/settings/integrations')->with('error', 'There was an error with integration (#2).');
            }
            $integration = new Integration;
            $integration->service_id = IntegrationService::SERVICE_GOOGLE_DRIVE;
            $integration->company_id = self::$user->company->company_id;
            $integration->token = $token;
            $integration->account = sprintf('%s (%s)', $userInfo->getName(), $userInfo->getEmail());
            $integration->save();
            return Redirect::to('/staff/settings/integrations')->with('message', 'Service connected.');
        }

        if ($serviceId == IntegrationService::SERVICE_FRESHBOOKS) {
            return Redirect::to('/staff/settings/integrations')->with('error', 'There was an error with integration (#1).');

            /*
            $token = Input::get('oauth_token', false);
            $verifier = Input::get('oauth_verifier', false);
            $domain = Session::get('integrations_freshbooks_domain', false);

            if (!$token || !$verifier || !$domain) {
                return Redirect::to('/staff/settings/integrations')->with('error', 'There was an error with integration (#1).');
            }


            $freshbooks = new Freshbooks(
                Config::get('panel.freshbooks_oauth_consumer_key'),
                Config::get('panel.freshbooks_oauth_consumer_secret'),
                URL::to('/staff/integrations/callback/' . $serviceId),
                $domain
            );

            try {
                $token = $freshbooks->getAccessToken($token, $verifier);
                $token['domain'] =  $domain;
                $freshbooks->setOauthToken($token['oauth_token']);
                $freshbooks->setOauthTokenSecret($token['oauth_token_secret']);
                $request = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<request method="system.current">
</request>
XML;
                $response = $freshbooks->post($request);
                $integration = new Integration;
                $integration->service_id = IntegrationService::SERVICE_FRESHBOOKS;
                $integration->company_id = self::$user->company->company_id;
                $integration->token = json_encode($token);
                $integration->account = $response->company_name;
                $integration->save();
            } catch (Exception $e) {
                return Redirect::to('/staff/settings/integrations')->with('error', 'There was an error with integration (#2).');
            }*/
        }

        App::abort(404);
    }

    public function integrationsFreshbooks() {
        $service = IntegrationService::where('service_id', IntegrationService::SERVICE_FRESHBOOKS)->first();
        if (!$service || !$service->isIntegrated(self::$user->company->company_id)) {
            App::abort(404);
        }

        return View::make('site.staff.settings_integration_freshbooks_settings');
    }

    public function integrationsFreshbooksSubmit() {
        $service = IntegrationService::where('service_id', IntegrationService::SERVICE_FRESHBOOKS)->first();
        if (!$service || !$service->isIntegrated(self::$user->company->company_id)) {
            App::abort(404);
        }

        if (Input::has('import')) {
            $results = Integrations::FreshbooksTokenGetContacts(self::$user->company->company_id);
            if (empty($results)) {
                return Redirect::to('/staff/settings/integrations/freshbooks')->with('error', 'An error occurred while importing accounts. Try to re-connect your FreshBooks account again. If problem persists, please, contact customer support.');
            }

            Session::put('integrations_freshbooks_results', $results);

            return View::make('site.staff.settings_integration_freshbooks_import', [
                'importResults' => $results
            ]);
        } elseif (Input::has('finish')) {
            $users = Input::get('users');
            $results = Session::get('integrations_freshbooks_results');
            
            /*
             * This will be sent to front-end to display results.
             */
            $importResults = [
                'success' => [],
                'failed' => []
            ];

            foreach ($users as $value) {
                $value = (int) $value;
                if (!isset($results[$value])) {
                    return Redirect::to('/staff/settings/integrations/freshbooks')->with('error', 'An error occurred while importing accounts. Try to re-connect your FreshBooks account again. If problem persists, please, contact customer support.');
                }
                
                $result = $results[$value];
                $customer = new User;
                $customer->company_id = self::$user->company->company_id;
                $customer->role = User::ROLE_CUSTOMER;
                $customer->username = $result['username'];
                $customer->email = $result['email'];
                $customer->email_cc = $result['email_cc'];
                $password = str_random(10);
                $customer->password = Hash::make($password);
                try {
                    $customer->save();
                    $customer->sendRegisteredEmail(['password' => $password]);
                    $importResults['success'][] = $customer;
                } catch (Exception $e) {
                    $importResults['failed'][] = $customer;
                }
            }

            Session::forget('integrations_freshbooks_results');
            return View::make('site.staff.settings_integration_freshbooks_import_completed', [
                'importResults' => $importResults
            ]);
        }

        App::abort(404);
    }

}