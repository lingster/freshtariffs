<?php
/**
 * File: PricelistController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

use \Chrisbjr\ApiGuard\Controllers\ApiGuardController;

class API_v1_PricelistController extends ApiGuardController {
    public function all() {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $pricelistsInfo = $company->pricelistsInfo()->paginate(100);
        return $this->response->withPaginator($pricelistsInfo, new PricelistTransformer, 'pricelists');
    }

    public function show($priceId) {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $pricelistInfo = $company->pricelistsInfo()->where('price_id', $priceId)->first();
        if (!$pricelistInfo) {
            return $this->response->errorNotFound();
        }
        return $this->response->withItem($pricelistInfo, new PricelistTransformer, 'pricelist');
    }

    public function create() {
        $company = Company::where('company_id', $this->apiKey->user_id)->first();
        $pricelistTypesSelect = PricelistCustomType::getTypes($company->company_id);
        $pricelistTypesValidation = array_values($pricelistTypesSelect);

        $validationRules = [
            'customer_id' => 'required|numeric',
            'effective_date' => 'required|date|after:yesterday',
            'type' => 'required|in:' . implode(',', $pricelistTypesValidation),
            'destinations' => 'required|array',
            'note' => 'sometimes|required|min:3'
        ];

        $destinations = Input::get('destinations');

        if (count($destinations) < 1) {
            return $this->response->errorWrongArgs('At least one destination must be selected.');
        }

        foreach ($destinations as $i => $destination) {
            $validationRules['destinations.' . $i . '.id'] = 'required|numeric|exists:destinations,destination_id';
            $validationRules['destinations.' . $i . '.rate'] = 'required|numeric|min:0';
        }

        $validator = Validator::make(Input::all(), $validationRules);
        if ($validator->fails()) {
            return $this->response->errorWrongArgsValidator($validator);
        }

        $user = $company->users()->where('user_id', Input::get('customer_id'))->where('role', User::ROLE_CUSTOMER)->first();
        if (!$user) {
            return $this->response->errorWrongArgs('User is not exist.');
        }

        $result = Pricelist::buildPricelistFromArray(Input::get('destinations'));
        $pricelist = $result['pricelist'];

        $pricelistInfo = Pricelist::storePricelist($user->user_id, $pricelist);
        $company->generateAndSendEmail($user->user_id);
        Integrations::GoogleDriveStorePricelist($company->company_id, $user->user_id, $pricelistInfo);
        Integrations::WebhookStorePricelist($company->company_id, $user->user_id, $pricelistInfo);

        return Response::json([
            'success' => [
                'code' => 'OK',
                'http_code' => 200,
                'message' => 'OK'
            ]
        ]);
    }
}