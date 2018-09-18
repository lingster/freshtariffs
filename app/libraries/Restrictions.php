<?php
/**
 * File: Restrictions.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class Restrictions
{
    const RESTRICTION_STAFF_COUNT = 'restriction_staff_count';
    const RESTRICTION_CUSTOMER_COUNT = 'restriction_customer_count';

    /**
     * Check if user has access to feature
     * @param User $user
     * @param string $feature
     * @return bool
     * @throws Exception
     */
    public static function hasAccess($user, $feature) {
        $subscriptionType = $user->getSubscriptionType();

        switch ($feature) {
            case self::RESTRICTION_CUSTOMER_COUNT:
                $customersCount = $user->company->users()->where('role', User::ROLE_CUSTOMER)->count();
                return $customersCount < self::getConfigKey($subscriptionType, $feature);

            case self::RESTRICTION_STAFF_COUNT:
                $staffCount = $user->company->users()->where('role', User::ROLE_COMPANY)->count();
                return $staffCount < self::getConfigKey($subscriptionType, $feature);

            default:
                throw new Exception('Unknown restriction: ' . $feature);
        }
    }
    
    public static function getConfigKey($subscriptionType, $feature) {
        return Config::get(sprintf('panel.%s_%s', $subscriptionType, $feature), '');
    }
}