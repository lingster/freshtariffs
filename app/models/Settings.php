<?php
/**
 * File: Settings.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Settings extends Eloquent {
    protected $table = 'sys_settings';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public static function getOption($key)
    {
        /** @var Settings $record */
        $record = Settings::where('key', $key)->first();
        if (!$record) {
            return NULL;
        }

        return $record->value;
    }

    public static function setOption($key, $value)
    {
        /** @var Settings $record */
        $record = Settings::where('key', $key)->first();
        if (!$record) {
            $record = new Settings;
            $record->key = $key;
        }

        $record->value = $value;
        $record->save();
    }

}