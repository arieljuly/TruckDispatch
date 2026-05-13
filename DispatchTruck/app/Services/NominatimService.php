<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NominatimService
{
    protected $client;
    protected $baseUrl = 'https://nominatim.openstreetmap.org';

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 10.0,
        ]);
    }

    /**
     * Search for locations by query string
     */
    public function search($query, $limit = 5)
    {
        try {
            $response = $this->client->get('/search', [
                'query' => [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => $limit,
                    'addressdetails' => 1,
                    'accept-language' => 'en',
                ],
                'headers' => [
                    'User-Agent' => 'DispatchTruck Application', // Required by Nominatim
                ]
            ]);

            $results = json_decode($response->getBody(), true);

            return array_map(function ($result) {
                return [
                    'lat' => $result['lat'],
                    'lon' => $result['lon'],
                    'display_name' => $result['display_name'],
                    'address' => $result['address'] ?? [],
                    'type' => $result['type'] ?? '',
                ];
            }, $results);

        } catch (\Exception $e) {
            Log::error('Nominatim search error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Reverse geocode - get address from coordinates
     */
    public function reverseGeocode($lat, $lon)
    {
        try {
            $response = $this->client->get('/reverse', [
                'query' => [
                    'lat' => $lat,
                    'lon' => $lon,
                    'format' => 'json',
                    'addressdetails' => 1,
                    'accept-language' => 'en',
                ],
                'headers' => [
                    'User-Agent' => 'DispatchTruck Application',
                ]
            ]);

            $result = json_decode($response->getBody(), true);

            return [
                'display_name' => $result['display_name'] ?? '',
                'address' => $result['address'] ?? [],
            ];

        } catch (\Exception $e) {
            Log::error('Nominatim reverse geocode error: ' . $e->getMessage());
            return null;
        }
    }
}