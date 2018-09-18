<?php
/**
 * File: Destination.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Destination extends Eloquent {
    protected $table = 'destinations';
    protected $primaryKey = 'destination_id';

    public $timestamps = false;

    public function pricelists() {
        return $this->hasMany('Pricelist');
    }
}