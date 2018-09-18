<?php
/**
 * File: ApiController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class APIController extends BaseController {
    public function destinations() {
        if (!Request::ajax()) {
            //App::abort(404);
        }

        $q = strtolower(Input::get('q', false));
        if (strlen($q) < 3) {
            return Response::json(['items' => []]);
        }
        $q .= '%';
        $type = Input::get('t', false);
        $userId = Input::get('u', false);
        $latestPricelistId = $userId ? Pricelist::getLatestPricelistId($userId, $type) : null;

        $result = ['items' => []];

        if (is_numeric($q)) {
            $destinations = Destination::whereRaw('prefix like ?', [$q]);
        } else {
            $destinations = Destination::whereRaw('LOWER(country) like ?', [$q]);
            if ($destinations->count() < 1) {
                $destinations = Destination::whereRaw('LOWER(network_name) like ?', [$q]);
            } else {
                $destinationsCountries = DB::table('destinations')
                    ->selectRaw('count(destination_id) as n, country')
                    ->whereRaw('LOWER(country) like ?', [$q])
                    ->groupBy('country')
                    ->get();
                foreach ($destinationsCountries as $country) {
                    $result['items'][] = [
                        'id' => $country->country,
                        'text' => sprintf('Add whole %s (%d items)', $country->country, $country->n),
                        'meta' => [
                            's' => true,
                            'c' => $country->country,
                            'n' => $country->n
                        ]
                    ];
                }
            }
        }

        $destinations = $destinations->get();
        foreach ($destinations as $destination) {
            if ($latestPricelistId) {
                $latestRate = Pricelist::where('price_id', $latestPricelistId)
                    ->where('destination_id', $destination->destination_id)
                    ->first(['rate']);
                $latestRate = isset($latestRate->rate) ? $latestRate->rate : null;
            } else {
                $latestRate = null;
            }
            $result['items'][] = [
                'id' => $destination->destination_id,
                // (35995549) Bulgaria - Olo (1/1)
                'text' => sprintf('(%d) %s - %s (%s)',
                    $destination->prefix,
                    $destination->country,
                    $destination->network_name,
                    $destination->interval),
                'meta' => [
                    'p' => $destination->prefix,
                    'i' => $destination->interval,
                    'd' => sprintf('%s - %s', $destination->country, $destination->network_name),
                    'r' => $latestRate
                ]
            ];
        }

        return Response::json($result);
    }

    public function destinationsCountry() {
        if (!Request::ajax()) {
            App::abort(404);
        }

        $q = Input::get('q', false);
        if (strlen($q) < 3) {
            return Response::json(['items' => []]);
        }

        $type = Input::get('t', false);
        $userId = Input::get('u', false);
        $latestPricelistId = $userId ? Pricelist::getLatestPricelistId($userId, $type) : null;

        $destinations = Destination::where('country', $q)->get();
        $result = [];
        foreach ($destinations as $destination) {
            if ($latestPricelistId) {
                $latestRate = Pricelist::where('price_id', $latestPricelistId)
                    ->where('destination_id', $destination->destination_id)
                    ->first(['rate']);
                $latestRate = isset($latestRate->rate) ? $latestRate->rate : null;
            } else {
                $latestRate = null;
            }
            $result['items'][] = [
                'id' => $destination->destination_id,
                // (35995549) Bulgaria - Olo (1/1)
                'text' => sprintf('(%d) %s - %s (%s)',
                    $destination->prefix,
                    $destination->country,
                    $destination->network_name,
                    $destination->interval),
                'meta' => [
                    'p' => $destination->prefix,
                    'i' => $destination->interval,
                    'd' => sprintf('%s - %s', $destination->country, $destination->network_name),
                    'r' => $latestRate
                ]
            ];
        }

        return $result;
    }

    public function subscription() {
        if (!Request::ajax()) {
            App::abort(404);
        }

        $plan = Input::get('plan', '');
        $token = Input::get('token', '');

        if (!in_array($plan, [User::SUBSCRIPTION_TYPE_FREE, User::SUBSCRIPTION_TYPE_SMALL, User::SUBSCRIPTION_TYPE_LARGE])) {
            return Response::json([
                'status' => 'error',
                'error' => [
                    'message' => 'Selected plan is invalid.'
                ]
            ]);
        }

        if (empty($token)) {
            return Response::json([
                'status' => 'error',
                'error' => [
                    'message' => 'Token is not valid.'
                ]
            ]);
        }

        try {
            if (self::$user->subscribed()) {
                self::$user->subscription($plan)->swap();
            } else {
                self::$user->subscription($plan)->create($token);
            }
        } catch(Exception $e) {
            return Response::json([
                'status' => 'error',
                'error' => [
                    'message' => $e->getMessage()
                ]
            ]);
        }
        
        return Response::json(['status' => 'ok']);
    }

    public function subscriptionSwap() {
        if (!Request::ajax()) {
            App::abort(404);
        }

        $plan = Input::get('plan', '');
        if (!in_array($plan, [User::SUBSCRIPTION_TYPE_FREE, User::SUBSCRIPTION_TYPE_SMALL, User::SUBSCRIPTION_TYPE_LARGE])) {
            return Response::json([
                'status' => 'error',
                'error' => [
                    'message' => 'Selected plan is invalid.'
                ]
            ]);
        }

        try {
            $currentPlan = self::$user->getSubscriptionType();
            if ($currentPlan === User::SUBSCRIPTION_TYPE_FREE) {
                return Response::json([
                    'status' => 'error',
                    'error' => [
                        'message' => 'User is not subscribed.'
                    ]
                ]);
            }

            if ($plan === User::SUBSCRIPTION_TYPE_FREE) {
                self::$user->subscription()->cancel();
            } else {
                self::$user->subscription($plan)->swap();
            }
        } catch(Exception $e) {
            return Response::json([
                'status' => 'error',
                'error' => [
                    'message' => $e->getMessage()
                ]
            ]);
        }

        return Response::json(['status' => 'ok']);

    }
}