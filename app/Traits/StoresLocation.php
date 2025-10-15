<?php

namespace App\Traits;

use App\Models\Location;

trait StoresLocation
{
    /**
     * تخزين موقع جديد في جدول locations
     *
     * @param array $data ['city' => ..., 'country' => ..., 'latitude' => ..., 'longitude' => ...]
     * @return Location
     */
    public function storeLocation(array $data): Location
    {
        return Location::create([
            'city'      => $data['city'] ?? null,
            'country'   => $data['country'] ?? null,
            'latitude'  => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);
    }
}
