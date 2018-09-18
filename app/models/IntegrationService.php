<?php
/**
 * File: IntegrationService.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */
class IntegrationService extends Eloquent
{
    const SERVICE_GOOGLE_DRIVE = 1;
    const SERVICE_WEBHOOK = 2;
    const SERVICE_API = 3;
    const SERVICE_FRESHBOOKS = 4;

    protected $table = 'sys_integrations_services';
    protected $primaryKey = 'service_id';
    public $timestamps = false;
    
    public function integrations() {
        return $this->hasMany('Integration', 'service_id', 'service_id');
    }

    public function isIntegrated($companyId) {
        return !!self::getIntegration($companyId);
    }
    
    public function getIntegration($companyId) {
        return Integration::where('company_id', $companyId)->where('service_id', $this->service_id)->first();
    }
}