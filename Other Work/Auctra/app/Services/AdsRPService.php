<?php

namespace App\Services;

use Illuminate\Support\Collection;

class AdsRPService
{
    public function injectAds(Collection $items,Collection $ads,int $every = 5): Collection {

        if ($items->isEmpty() || $ads->isEmpty()) {
            return $items;
        }

        $items = $items->values();

        $adIndex = 0;

        for ($i = $every; $i < $items->count(); $i += ($every + 1)) {

            $ad = $ads[$adIndex];

            $ad->item_type = 'ad';

            $items->splice($i, 0, [$ad]);

            $adIndex++;

            if ($adIndex >= $ads->count()) { // ? if the ads ended return from the loop
                $adIndex = 0;
            }
        }

        return $items->values();
    }
}