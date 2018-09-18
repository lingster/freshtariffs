<?php
/**
 * File: Pricelist.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Pricelist extends Eloquent {
    static $destinationCache = [];

    const PRICELIST_DEFAULT_TYPES = [
        'CLI' => 'CLI',
        'Standard' => 'Standard',
        'Platinum' => 'Platinum',
        'Gold' => 'Gold'
    ];
    protected $table = 'pricelists';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function user() {
        return $this->hasOne('User', 'user_id', 'user_id');
    }

    public static function getLatestPricelistId($userId, $type = false) {
        $record = PricelistInfo::where('user_id', $userId);
        if ($type !== FALSE) {
            $record = $record->where('price_type', $type);
        }
        $record = $record->orderBy('created_at', 'desc')->first();
        return $record ? $record->price_id : null;
    }

    public static function dropExistingPricelist($userId, $type = false) {
        $latestPricelistId = self::getLatestPricelistId($userId, $type);
        if ($latestPricelistId) {
            $existingPricelists = PricelistInfo::where('user_id', $userId)->where('price_id', '!=', $latestPricelistId);
            if ($type !== FALSE) {
                $existingPricelists = $existingPricelists->where('price_type', $type);
            }
            $existingPricelists = $existingPricelists->get();
            /** @var PricelistInfo $existingPricelist */
            foreach ($existingPricelists as $existingPricelist) {
                /** @var Pricelist $pricelist */
                $pricelists = $existingPricelist->pricelists;
                foreach ($pricelists as $pricelist) {
                    $pricelist->delete();
                }
            }
        }
    }

    public static function generateXlsPricelist($userId, $previousPricelistId) {
        $user = User::where('user_id', $userId)->first();

        $latestPricelistId = self::getLatestPricelistId($userId);
        $priceListInfo = PricelistInfo::where('price_id', $latestPricelistId)->first();
        $latestPricelist = Pricelist::where('price_id', $latestPricelistId)->get();
        $latestPricelistData = [];
        $previousPricelist = Pricelist::where('price_id', $previousPricelistId)->get();
        $previousPricelistData = [];

        $dateWithTime = \Carbon\Carbon::parse($priceListInfo->effective_date)->format($user->company->date_format . ' H:i');
        $dateWithoutTime = \Carbon\Carbon::parse($priceListInfo->effective_date)->format($user->company->date_format);

        foreach ($previousPricelist as $previousPricelistItem) {
            $previousPricelistData[$previousPricelistItem->destination_id] = [
                'rate' => $previousPricelistItem->rate
            ];
        }

        foreach ($latestPricelist as $latestPricelistItem) {
            if (!isset(self::$destinationCache[$latestPricelistItem->destination_id])) {
                /** @var Destination $destination */
                $destination = Destination::where('destination_id', $latestPricelistItem->destination_id)->first();
                self::$destinationCache[$latestPricelistItem->destination_id] = $destination;
            } else {
                $destination = self::$destinationCache[$latestPricelistItem->destination_id];
            }
            // item not exists in previous price list
            if (!isset($previousPricelistData[$latestPricelistItem->destination_id])) {
                $comment = 'New Destination';
            } else {
                $previousRate = floatval($previousPricelistData[$latestPricelistItem->destination_id]['rate']);
                $newRate = floatval($latestPricelistItem->rate);
                if ($newRate == -1) {
                    $comment = 'Blocked';
                    $latestPricelistItem->rate = '-';
                } elseif ($previousRate == -1) {
                    $comment = 'New destination';
                } else {
                    if ($previousRate === $newRate) {
                        $comment = 'Not Changed';
                    } elseif ($previousRate > $newRate) {
                        $comment = 'Decreased';
                    } else {
                        $comment = 'Increased';
                    }
                }
            }

            $latestPricelistData[] = [
                'prefix' => $destination->prefix,
                'destination' => sprintf('%s - %s', $destination->country, $destination->network_name),
                'rate' => $latestPricelistItem->rate,
                'effective_date' => $dateWithTime,
                'interval' => $destination->interval,
                'comment' => $comment
            ];
        }

        $username = $user->username;
        // CLI_Price_List_From_03.04.2016_for_Edward_Snowden_%ID%.xls
        $filename = sprintf('%s_Price_List_From_%s_for_%s_%s',
            $priceListInfo->price_type,
            $dateWithoutTime,
            str_replace(' ', '_', $username),
            $latestPricelistId
        );
        $filename = preg_replace('/[^a-zA-Z0-9-_\.]/','', $filename);
        Excel::create($filename, function($excel) use ($latestPricelistData, $dateWithTime, $username, $priceListInfo) {
            $excel->sheet('Price List', function($sheet) use ($latestPricelistData, $dateWithTime, $username, $priceListInfo) {
                $sheet->loadView('sheets.pricelist',
                    [
                        'username' => $username,
                        'date' => $dateWithTime,
                        'pricelist' => $latestPricelistData,
                        'type' => $priceListInfo->price_type
                    ]);
            });
        })->store('xls', public_path('pricelists/'));
        return $filename . '.xls';
    }

    public static function buildPricelistFromPostData($postData) {
        $pricelist = [];
        $validationRules = [];
        foreach ($postData as $key => $value) {
            if (substr_count($key, '_') < 1) {
                continue;
            }

            list($type, $destinationId) = explode('_', $key);
            if (!in_array($type, ['rate']) || !is_numeric($destinationId) || empty($value)) {
                continue;
            }

            $validationRules['rate_' . $destinationId] = 'numeric';

            /** @var Destination $destination */
            $destination = Destination::where('destination_id', $destinationId)->first();
            if (!$destination) {
                continue;
            }

            $pricelist[] = [
                'destination_id' => $destinationId,
                'value' => $value,
                'prefix' => $destination->prefix,
                'destination' => sprintf('%s - %s', $destination->country, $destination->network_name),
                'interval' => $destination->interval
            ];
        }

        return ['validation' => $validationRules, 'pricelist' => $pricelist];
    }

    public static function buildPricelistFromArray($array) {
        $pricelist = [];
        foreach ($array as $value) {
            if (!is_numeric($value['rate'])) {
                continue;
            }

            /** @var Destination $destination */
            $destination = Destination::where('destination_id', $value['id'])->first();
            if (!$destination) {
                continue;
            }

            $pricelist[] = [
                'destination_id' => $destination->destination_id,
                'value' => $value['rate'],
                'prefix' => $destination->prefix,
                'destination' => sprintf('%s - %s', $destination->country, $destination->network_name),
                'interval' => $destination->interval
            ];
        }

        return ['validation' => [], 'pricelist' => $pricelist];
    }

    public static function storePricelist($userId, $pricelist) {
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return NULL;
        }

        $type = Input::get('type');
        $effectiveDate = Input::get('effective_date');
        $previousPricelistId = Pricelist::getLatestPricelistId($userId, $type);
        Pricelist::dropExistingPricelist($userId, $type);
        $priceId = Uuid::generate();
        foreach ($pricelist as $data) {
            $pricelistModel = new Pricelist;
            $pricelistModel->price_id = $priceId;
            $pricelistModel->destination_id = $data['destination_id'];
            $pricelistModel->rate = $data['value'];
            $pricelistModel->save();
        }

        $pricelistInfo = new PricelistInfo;
        $pricelistInfo->company_id = $user->company->company_id;
        $pricelistInfo->price_id = $priceId;
        $pricelistInfo->user_id = $userId;
        $pricelistInfo->price_type = $type;
        $pricelistInfo->effective_date = $effectiveDate;
        $pricelistInfo->price_note = Input::get('note');
        $pricelistInfo->save();

        $filename = Pricelist::generateXlsPricelist($userId, $previousPricelistId);
        $pricelistInfo->filename = $filename;
        $pricelistInfo->save();
        $user->latest_price_filename = $filename;
        $user->save();

        return $pricelistInfo;
    }
}