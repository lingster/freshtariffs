<?php
/**
 * File: PricelistTransformer.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class PricelistTransformer extends \League\Fractal\TransformerAbstract {
    public function transform(PricelistInfo $pricelistInfo) {
        return [
            'id' => $pricelistInfo->price_id,
            'customer_id' => $pricelistInfo->user_id,
            'type' => $pricelistInfo->price_type,
            'effective_date' => \Carbon\Carbon::parse($pricelistInfo->effective_date)->startOfDay(),
            'url' => URL::to('/pricelists/' . $pricelistInfo->filename),
            'created_at' => $pricelistInfo->created_at
        ];
    }
}