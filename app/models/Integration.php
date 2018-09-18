<?php
/**
 * File: Integration.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Integration extends Eloquent
{
    protected $table = 'sys_integrations';
    protected $primaryKey = 'id';

    public function company() {
        return $this->hasOne('Company', 'company_id', 'company_id');
    }
}