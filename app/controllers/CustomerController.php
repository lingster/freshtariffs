<?php
/**
 * File: CustomerController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class CustomerController extends BaseController {
    public function dashboard() {
        $latestPriceFilename = self::$user->latest_price_filename;
        return View::make('site.customer.dashboard', [
            'latestPriceFilename' => $latestPriceFilename
        ]);
    }
}