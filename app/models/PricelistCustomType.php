<?php
/**
 * File: PricelistCustomType.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class PricelistCustomType extends Eloquent {
    protected $table = 'pricelists_custom_types';
    protected $primaryKey = 'custom_type_id';
    public $timestamps = false;

    public static function getGlobalCustomTypes() {
        $result = [];
        $globalCustomTypes = PricelistCustomGlobalType::get();
        foreach ($globalCustomTypes as $globalCustomType) {
            $result[$globalCustomType->value] = $globalCustomType->value;
        }

        return $result;
    }

    public static function getCustomTypes($companyId) {
        $result = [];
        $customTypes = PricelistCustomType::where('company_id', $companyId)->get();
        foreach ($customTypes as $customType) {
            $result[$customType->value] = $customType->value;
        }

        return $result;
    }

    public static function getTypes($companyId) {
        $pricelistTypesSelect = Pricelist::PRICELIST_DEFAULT_TYPES;
        $pricelistTypesSelect = array_merge($pricelistTypesSelect, PricelistCustomType::getCustomTypes($companyId));
        $pricelistTypesSelect = array_merge($pricelistTypesSelect, PricelistCustomType::getGlobalCustomTypes());
        return $pricelistTypesSelect;
    }
}

class PricelistCustomGlobalType extends Eloquent {
    protected $table = 'pricelists_global_custom_types';
    protected $primaryKey = 'global_custom_type_id';
    public $timestamps = false;
}