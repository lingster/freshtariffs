<?php
use Akeneo\Component\SpreadsheetParser\SpreadsheetParser;
/**
 * File: AdminController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class AdminController extends BaseController {
    public function test() {
        dd(time());
    }

    public function index() {
        return View::make('site.admin.index');
    }

    public function settings() {
        return View::make('site.admin.settings', [
            'footer_template' => Settings::getOption('footer_template'),
            'default_email_template' => Settings::getOption('default_email_template'),
            'default_email_subject' => Settings::getOption('default_email_subject'),
            'registered_email_template' => Settings::getOption('registered_email_template'),
            'registered_email_subject' => Settings::getOption('registered_email_subject'),
        ]);
    }

    public function settingsSubmit() {
        $validatorRules = [
            'default_email_template' => 'required',
            'default_email_subject' => 'required',
            'registered_email_template' => 'required',
            'registered_email_subject' => 'required'
        ];

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validatorRules);

        if ($validator->fails()) {
            return Redirect::to('/admin/settings')->withErrors($validator->errors())->withInput(Input::all());
        }

        Settings::setOption('footer_template', Input::get('footer_template'));
        Settings::setOption('default_email_template', Input::get('default_email_template'));
        Settings::setOption('default_email_subject', Input::get('default_email_subject'));
        Settings::setOption('registered_email_template', Input::get('registered_email_template'));
        Settings::setOption('registered_email_subject', Input::get('registered_email_subject'));
        return Redirect::to('/admin/settings')->with('message', 'Changes saved.');
    }

    public function customTypes() {
        $customTypes = PricelistCustomGlobalType::all();
        return View::make('site.admin.customtypes', ['custom_types' => $customTypes]);
    }

    public function customTypesCreate() {
        return View::make('site.admin.customtypes_create');
    }

    public function customTypesCreateSubmit() {
        $validatorRules = array(
            'type' => 'required',
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validatorRules);

        if ($validator->fails()) {
            return Redirect::to('/admin/customtypes/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $customPricelistType = new PricelistCustomGlobalType;
        $customPricelistType->value = Input::get('type');
        $customPricelistType->save();
        return Redirect::to('/admin/customtypes')->with('message', 'Type created');
    }

    public function customTypesEdit($typeId) {
        /** @var PricelistCustomGlobalType $customPricelistType */
        $customPricelistType = PricelistCustomGlobalType::where('global_custom_type_id', $typeId)->first();
        if (!$customPricelistType) {
            App::abort(404);
        }

        return View::make('site.admin.customtypes_edit', ['custom_type' => $customPricelistType]);
    }

    public function customTypesEditSubmit($typeId) {
        /** @var PricelistCustomGlobalType $customPricelistType */
        $customPricelistType = PricelistCustomGlobalType::where('global_custom_type_id', $typeId)->first();
        if (!$customPricelistType) {
            App::abort(404);
        }
        $validatorRules = array(
            'type' => 'required',
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validatorRules);

        if ($validator->fails()) {
            return Redirect::to('/admin/customtypes/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $customPricelistType->value = Input::get('type');
        $customPricelistType->save();
        return Redirect::to('/admin/customtypes')->with('message', 'Type edited');
    }

    public function customTypesDelete($typeId)
    {
        /** @var PricelistCustomGlobalType $customPricelistType */
        $customPricelistType = PricelistCustomGlobalType::where('global_custom_type_id', $typeId)->first();
        if (!$customPricelistType) {
            App::abort(404);
        }
        $customPricelistType->delete();
        return Redirect::to('/admin/customtypes')->with('message', 'Type deleted.');
    }
    
    public function companies() {
        $companies = Company::all();
        return View::make('site.admin.companies', ['companies' => $companies]);
    }

    public function companiesCreate() {
        return View::make('site.admin.companies_create');
    }

    public function companiesCreateSubmit() {
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
            return Redirect::to('/admin/companies/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $subdomainName = strtolower(preg_replace('/[^a-zA-Z0-9\-_]/', '', Input::get('name')));
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
        $company->name = Input::get('name');
        $company->subdomain = $subdomainName;
        $company->email = Input::get('email');
        $company->address = Input::get('address');
        $company->phone = Input::get('phone');
        $company->website = Input::get('website');
        $company->email_template = Input::get('email_template');
        $company->email_subject = Input::get('email_subject');
        $company->email_reply_to = Input::get('email_reply_to');
        $company->date_format = Input::get('date_format');
        $company->save();

        return Redirect::to('/admin/companies')->with('message', 'Company created');
    }

    public function companiesEdit($companyId) {
        /** @var Company $company */
        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            App::abort(404);
        }

        return View::make('site.admin.companies_edit', ['company' => $company]);
    }

    public function companiesEditSubmit($companyId) {
        /** @var Company $company */
        $company = Company::where('company_id', $companyId)->first();
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
            return Redirect::to('/admin/companies/' . $companyId . '/edit')->withErrors($validator->errors())->withInput(Input::all());
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

        return Redirect::to('/admin/companies')->with('message', 'Company edited');
    }

    public function companiesDelete($companyId) {
        /** @var Company $company */
        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            App::abort(404);
        }

        $company->deleteWithCleanup();
        return Redirect::to('/admin/companies')->with('message', 'Company deleted');
    }

    public function destinations() {
        $destinations = Destination::paginate(50);
        return View::make('site.admin.destinations', ['destinations' => $destinations]);
    }

    public function destinationsSearch() {
        $query = Input::get('query');
        if (is_numeric($query)) {
            $destinations = Destination::whereRaw('prefix like ?', [$query . '%'])->paginate(50);
        } else {
            $destinations = Destination::whereRaw('LOWER(country) like ?', [$query . '%'])
                ->orWhereRaw('LOWER(network_name) like ?', [$query . '%'])
                ->paginate(50);
        }

        return View::make('site.admin.destinations', ['destinations' => $destinations]);
    }

    public function destinationsDownload() {
        $destinations = Destination::get();
        $filename = public_path('pricelists/destinations-export.csv');
        if (file_exists($filename)) {
            @unlink($filename);
        }

        $fp = fopen($filename, 'w');
        fputcsv($fp, ['Prefix', 'Country', 'Network name', 'Interval']);
        foreach ($destinations as $destination) {
            fputcsv($fp, [$destination->prefix, $destination->country, $destination->network_name, $destination->interval]);
        }
        fclose($fp);
        return Response::download($filename);
    }

    public function destinationsSubmit() {
        $input = Input::except(['_token']);
        $map = [];
        $prefixes = [];

        foreach ($input as $key => $value) {
            if (substr_count($key, '_') < 1) {
                continue;
            }

            list($type, $destinationId) = explode('_', $key);
            if (!in_array($type, ['prefix', 'country', 'networkname', 'interval']) || !is_numeric($destinationId)) {
                continue;
            }

            if ($type === 'networkname') {
                $type = 'network_name'; // as column and input have different names
            }

            // need to check prefix before saving
            if ($type === 'prefix') {
                if (!is_numeric($value)) {
                    return Redirect::to('/admin/destinations')->withInput(Input::all())->with('message', sprintf('Prefix "%s" is not numeric', $value));
                }

                // not unique prefix
                /** @var Destination $destination */
                $destination = Destination::where('prefix', $value)->first(['destination_id']);
                if (in_array($value, $prefixes) || ($destination && $destination->destination_id != $destinationId)) {
                    return Redirect::to('/admin/destinations')->withInput(Input::all())->with('message', sprintf('Prefix "%s" used more than once.', $value));
                }

                $prefixes[] = $value;
            }

            if (!isset($map[$destinationId])) {
                $map[(string)$destinationId] = [];
            }

            $map[(string)$destinationId][$type] = $value;
        }


        foreach ($map as $destinationId => $properties) {
            Destination::where('destination_id', $destinationId)->update($properties);
        }
        $redirectUrl = '/admin/destinations';
        $redirectParams = [];
        if (Input::has('query')) {
            $redirectUrl .= '/search';
            $redirectParams['query'] = Input::get('query');
        }
        if (Input::has('page')) {
            $redirectParams['page'] = Input::get('page');
        }

        return Redirect::to($redirectUrl . '?' . http_build_query($redirectParams))->with('message', 'Changes saved.');
    }

    public function destinationsAddSubmit() {
        $validationRules = array(
            'prefix' => 'required|numeric|unique:destinations',
            'country' => 'required|min:5',
            'network_name' => 'required|min:5',
            'interval' => 'required'
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Response::json(['status' => 'error', 'errors' => $validator->errors()]);
        }

        $destination = new Destination;
        $destination->prefix = Input::get('prefix');
        $destination->country = Input::get('country');
        $destination->network_name = Input::get('network_name');
        $destination->interval = Input::get('interval');
        $destination->save();

        return Response::json([
            'status' => 'ok',
            'template' => View::make('site.admin.destinations__row', ['destination' => $destination])->render()
        ]);
    }

    public function destinationsImport() {
        return View::make('site.admin.destinations_import');
    }

    public function destinationsImportSubmit() {
        $validationRules = array(
            'file' => 'required|mimes:txt,csv',
            'separator' => 'required|min:1|max:1'
        );

        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return Redirect::to('/admin/destinations/import')->withErrors($validator->errors())->withInput(Input::all());
        }

        $file = Input::file('file');
        if (!$file || !$file->isValid()) {
            $validator->messages()->add('file', 'Uploaded file is not valid.');
            return Redirect::to('/admin/destinations/import')->withErrors($validator->errors())->withInput(Input::all());
        }

        $file = array_map('trim', file($file->getRealPath(), FILE_IGNORE_NEW_LINES));
        $separator = Input::get('separator');
        $result = [
            'imported' => 0,
            'ignored' => []
        ];
        $import = [];

        foreach ($file as $n => $line) {
            $parts = explode($separator, $line);
            $parts = array_values(array_filter($parts, function($arg) {
                return !empty($arg);
            }));

            if (count($parts) !== 3) {
                $validator->messages()->add('file', sprintf('Line %d of file contains invalid or excess values: %s.',
                    $n + 1,
                    '(' . implode('), (', $parts) . ')'
                ));
                $validator->messages()->add('file', 'Make sure that you have chosen right separator.');
                return Redirect::to('/admin/destinations/import')->withErrors($validator->errors())->withInput(Input::all());
            }

            list($prefix, $destination, $interval) = $parts;
            if (!is_numeric($prefix)) {
                $validator->messages()->add('file', sprintf('Line %d of file contains invalid prefix (must be numeric): "%s".',
                    $n + 1,
                    $prefix
                ));
                return Redirect::to('/admin/destinations/import')->withErrors($validator->errors())->withInput(Input::all());
            }

            if (strlen($destination) < 5) {
                $validator->messages()->add('file', sprintf('Line %d of file contains invalid destination (must be min 5 characters): "%s".',
                    $n + 1,
                    $destination
                ));
                return Redirect::to('/admin/destinations/import')->withErrors($validator->errors())->withInput(Input::all());
            }

            $destinationModel = Destination::where('prefix', $prefix)->first();
            if ($destinationModel) {
                $result['ignored'][] = ['line' => $n+1, 'prefix' => $prefix];
                continue;
            }

            $import[] = ['prefix' => $prefix, 'destination' => $destination, 'interval' => $interval];
        }

        foreach ($import as $value) {
            $destinationModel = new Destination;
            $destinationModel->prefix = $value['prefix'];
            $destinationModel->destination = $value['destination'];
            $destinationModel->interval = $value['interval'];
            $destinationModel->save();
            $result['imported']++;
        }

        return View::make('site.admin.destinations_import_complete', ['result' => $result]);
    }

    public function users($companyId) {
        /** @var Company $company */
        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            App::abort(404);
        }

        $staffOnly = Input::has('staff');
        $companyUsers = $company->users()->where('role', $staffOnly ? User::ROLE_COMPANY : User::ROLE_CUSTOMER)->get();
        return View::make($staffOnly ? 'site.admin.users_staff' : 'site.admin.users', ['users' => $companyUsers, 'company' => $company]);
    }

    public function usersCreate($companyId) {
        /** @var Company $company */
        $company = Company::where('company_id', $companyId)->first();
        if (!$company) {
            App::abort(404);
        }

        return View::make('site.admin.users_create', ['company' => $company]);
    }

    public function usersCreateSubmit($companyId) {
        /** @var Company $company */
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
            'role' => 'required|in:' . implode(',', [User::ROLE_ADMIN, User::ROLE_COMPANY, User::ROLE_CUSTOMER])
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::to('/admin/users/' . $companyId . '/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $email = Input::get('email');
        if (User::where('company_id', $companyId)->where('email', $email)->count() > 0) {
            $validator->messages()->add('email', 'The email has already been taken.');
            return Redirect::to('/admin/users/' . $companyId . '/create')->withErrors($validator->errors())->withInput(Input::all());
        }

        $user = new User;
        $user->company_id = $companyId;
        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->email_cc = Input::get('email_cc');
        $user->email_bcc = Input::get('email_bcc');
        $user->password = Hash::make(Input::get('password'));
        $user->role = Input::get('role');
        $user->save();
        $user->sendRegisteredEmail();

        return Redirect::to('/admin/users/' . $companyId)->with('message', 'User created.');
    }

    public function usersEdit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            App::abort(404);
        }

        $company = $user->company;
        return View::make('site.admin.users_edit', [
            'company' => $company,
            'selectedUser' => $user
        ]);
    }

    public function usersEditSubmit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            App::abort(404);
        }

        $validationRules = array(
            'username' => 'required',
            'email' => 'required|email',
            'email_cc' => 'email',
            'email_bcc' => 'email',
            'password' => 'min:3',
            'role' => 'required|in:' . implode(',', [User::ROLE_ADMIN, User::ROLE_COMPANY, User::ROLE_CUSTOMER]),
        );

        Input::merge(array_map('trim', Input::all()));
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::to('/admin/users/' . $user->user_id . '/edit')->withErrors($validator->errors())->withInput(Input::all());
        }

        $email = Input::get('email');
        if ($email != $user->email && User::where('company_id', $user->company_id)->where('email', $email)->count() > 0) {
            $validator->messages()->add('email', 'The email has already been taken.');
            return Redirect::to('/admin/users/' . $user->user_id . '/edit')->withErrors($validator->errors())->withInput(Input::all());
        }

        $company = $user->company;

        $user->username = Input::get('username');
        $user->email = Input::get('email');
        $user->email_cc = Input::get('email_cc');
        $user->email_bcc = Input::get('email_bcc');

        if (!empty(Input::get('password'))) {
            $user->password = Hash::make(Input::get('password'));
        }

        $user->role = Input::get('role');
        $user->save();
        return Redirect::to('/admin/users/' . $company->company_id)->with('message', 'User edited.');
    }

    public function usersDelete($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            App::abort(404);
        }

        if ($userId == self::$user['user_id']) {
            App::abort(500);
        }

        $company = $user->company;
        $user->deleteWithCleanup();
        return Redirect::to('/admin/users/' . $company->company_id)->with('message', 'User deleted.');
    }

    public function price($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
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

        return View::make('site.admin.price', [
            'selectedUser' => $user,
            //'latestPricelistValues' => $latestPricelistValues,
            'companyUsers' => $companyUsers,
            'companyUsersSelect' => $companyUsersSelect,
            'pricelistTypesSelect' => $pricelistTypesSelect
        ]);
    }

    public function priceSubmit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            App::abort(404);
        }

        /** @var Company $company */
        $company = $user->company;
        $validationRules = array();
        $validationRules['users'] = 'required|array';
        $validationRules['effective_date'] = 'required|after:yesterday';

        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);
        $pricelistTypesValidation = array_values($pricelistTypesSelect);
        $validationRules['type'] = 'required|in:' . implode(',', $pricelistTypesValidation);


        $pricelistData = Input::except(['_token', 'users', 'type', 'note', 'effective_date']);
        $result = Pricelist::buildPricelistFromPostData($pricelistData);
        $pricelist = $result['pricelist'];
        $validationRules = array_merge($result['validation'], $validationRules);
        $validator = Validator::make(Input::all(), $validationRules);

        if ($validator->fails()) {
            return Redirect::to('/admin/price/' . $userId)->withErrors($validator->errors())->withInput(Input::all())->with('pricelist', $pricelist);
        }


        if (count($pricelist) < 1) {
            $validator->messages()->add('rate', 'At least one destination must be selected.');
            return Redirect::to('/admin/price/' . $userId)->withErrors($validator->errors())->withInput(Input::all())->with('pricelist', $pricelist);
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
                return Redirect::to('/admin/price/' . $userId)->withErrors($validator->errors())->withInput(Input::all())->with('pricelist', $pricelist);
            }
        }

        foreach ($users as $selectedUserId) {
            Pricelist::storePricelist($selectedUserId, $pricelist);
            $company->generateAndSendEmail($selectedUserId);
        }

        return Redirect::to('/admin/users/' . $user->company_id)->with('message', 'Price list created.');
    }

    public function priceXls($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            App::abort(404);
        }

        $company = $user->company;
        $companyUsers = $company->users;
        $companyUsersSelect = [];
        foreach ($companyUsers as $companyUser) {
            $companyUsersSelect[$companyUser->user_id] = $companyUser->username;
        }

        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);

        return View::make('site.admin.price_xls', [
            'selectedUser' => $user,
            'companyUsers' => $companyUsers,
            'companyUsersSelect' => $companyUsersSelect,
            'pricelistTypesSelect' => $pricelistTypesSelect
        ]);
    }

    public function priceXlsSubmit($userId) {
        /** @var User $user */
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            App::abort(404);
        }
        $company = $user->company;
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
            return Redirect::to('/admin/price/' . $userId . '/xls')->withErrors($validator->errors())->withInput(Input::all());
        }

        $file = Input::file('file');
        $filename = Str::random() . '.xlsx';
        if (!$file || !$file->isValid()) {
            $validator->messages()->add('file', 'Uploaded file is not valid.');
            return Redirect::to('/admin/price/' . $userId . '/xls')->withErrors($validator->errors())->withInput(Input::all());
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
                return Redirect::to('/admin/price/' . $userId . '/xls')->withErrors($validator->errors())->withInput(Input::all());
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
            Pricelist::storePricelist($selectedUserId, $pricelist);
            $company->generateAndSendEmail($selectedUserId);
        }

        return Redirect::to('/admin/users/' . $user->company_id)->with('message', 'Price list created.');
    }
}