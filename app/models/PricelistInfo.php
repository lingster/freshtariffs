<?php
/**
 * File: PricelistInfo.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class PricelistInfo extends Eloquent {
    protected $table = 'pricelists_info';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->hasOne('User', 'user_id', 'user_id');
    }

    public function pricelists()
    {
        return $this->hasMany('Pricelist', 'price_id', 'price_id');
    }
}