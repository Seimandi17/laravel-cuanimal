<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ProductGeocoder
{
    public function resolve(array $data): array
    {
        if (!empty($data['lat']) && !empty($data['lng'])) {
            return [
                'lat' => $data['lat'],
                'lng' => $data['lng'],
            ];
        }

        $parts = array_filter([
            $data['address'] ?? null,
            $data['city'] ?? null,
            $data['codigo_postal'] ?? null,
            $data['province'] ?? null,
            'España',
        ]);

        if (count($parts) <= 1) {
            return [];
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Cuanimal/1.0',
                ])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => implode(', ', $parts),
                    'format' => 'json',
                    'limit' => 1,
                    'addressdetails' => 0,
                ]);

            if (!$response->ok()) {
                return [];
            }

            $result = $response->json()[0] ?? null;

            if (!$result || !isset($result['lat'], $result['lon'])) {
                return [];
            }

            return [
                'lat' => $result['lat'],
                'lng' => $result['lon'],
            ];
        } catch (\Throwable) {
            return [];
        }
    }
}
