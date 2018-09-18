<?php
/**
 * File: DestinationTransformer.php
 * Created by bafoed.
 * This file is part of FreshTariffsApp project.
 * Do not modify if you do not know what to do.
 * 2016.
 */

class DestinationTransformer extends \League\Fractal\TransformerAbstract
{
    public function transform(Destination $destination) {
        return [
            'id' => $destination->destination_id,
            'prefix' => $destination->prefix,
            'name' => sprintf('(%s) %s - %s (%s)', $destination->prefix, $destination->country, $destination->network_name, $destination->interval)
        ];
    }
}