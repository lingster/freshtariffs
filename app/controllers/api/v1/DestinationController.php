<?php
/**
 * File: DestinationController.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */
use \Chrisbjr\ApiGuard\Controllers\ApiGuardController;


class API_v1_DestinationController extends ApiGuardController {
    public function all() {
        $destinations = Destination::paginate(1000);
        return $this->response->withPaginator($destinations, new DestinationTransformer, 'destinations');
    }
    
    public function search() {
        $query = strtolower(Input::get('query'));
        if (strlen($query) < 3) {
            return $this->response->errorWrongArgs('Query must be more than 3 character.');
        }
        $query .= '%';
        if (is_numeric($query)) {
            $destinations = Destination::whereRaw('prefix like ?', [$query]);
        } else {
            $destinations = Destination::whereRaw('LOWER(country) like ?', [$query]);
            if ($destinations->count() < 1) {
                $destinations = Destination::whereRaw('LOWER(network_name) like ?', [$query]);
            }
        }

        $destinations = $destinations->paginate(100);
        return $this->response->withPaginator($destinations, new DestinationTransformer, 'destinations');
    }
}