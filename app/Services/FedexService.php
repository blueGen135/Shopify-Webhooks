<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FedexService
{
  private $useDummyData;
  private $apiKey;
  private $apiSecret;
  private $accountNumber;
  private $meterNumber;
  private $environment;
  private $rateEndpoint;
  private $trackingEndpoint;
  private $oauthEndpoint;

  public function __construct()
  {
    // Load from database settings
    $this->useDummyData = Setting::where('key', 'fedex_use_dummy')->first()->value ?? true;
    $this->apiKey = Setting::where('key', 'fedex_api_key')->first()->value ?? null;
    $this->apiSecret = Setting::where('key', 'fedex_secret_key')->first()->value ?? null;
    $this->accountNumber = Setting::where('key', 'fedex_account_number')->first()->value ?? null;
    $this->meterNumber = Setting::where('key', 'fedex_meter_number')->first()->value ?? null;
    $this->environment = Setting::where('key', 'fedex_environment')->first()->value ?? 'sandbox';

    // Set endpoints based on environment
    if ($this->environment === 'production') {
      $this->rateEndpoint = 'https://apis.fedex.com/rate/v1/rates/quotes';
      $this->trackingEndpoint = 'https://apis.fedex.com/track/v1/trackingnumbers';
      $this->oauthEndpoint = 'https://apis.fedex.com/oauth/token';
    } else {
      $this->rateEndpoint = 'https://apis-sandbox.fedex.com/rate/v1/rates/quotes';
      $this->trackingEndpoint = 'https://apis-sandbox.fedex.com/track/v1/trackingnumbers';
      $this->oauthEndpoint = 'https://apis-sandbox.fedex.com/oauth/token';
    }
  }

  /**
   * Get shipping rates from multiple warehouses to customer address
   *
   * @param array $customerAddress - Customer's shipping address
   * @param array $items - Array of items with weight and dimensions
   * @param array $warehouses - Optional array of specific warehouses to check
   * @return array - Array of warehouse options with shipping costs, sorted by cost
   */
  public function getShippingRates($customerAddress, $items, $warehouses = null)
  {
    if ($this->useDummyData) {
      return $this->getDummyShippingRates($customerAddress, $items, $warehouses);
    }

    return $this->getRealShippingRates($customerAddress, $items, $warehouses);
  }

  /**
   * Get dummy shipping rates for local development
   */
  private function getDummyShippingRates($customerAddress, $items, $warehouses = null)
  {
    // Default warehouses if none provided
    if (empty($warehouses)) {
      $warehouses = $this->getWarehousesFromDatabase();
    }

    $rates = [];

    foreach ($warehouses as $warehouse) {
      // Calculate dummy distance based on coordinates
      $distance = $this->calculateDistance(
        $warehouse['latitude'] ?? 0,
        $warehouse['longitude'] ?? 0,
        $customerAddress['latitude'] ?? 0,
        $customerAddress['longitude'] ?? 0
      );

      // Calculate shipping cost (simplified formula for dummy data)
      $baseRate = 50.00;
      $perMileRate = 1.50;
      $weightFactor = $this->getTotalWeight($items) * 0.50;

      $shippingCost = $baseRate + ($distance * $perMileRate) + $weightFactor;

      // Add some randomness to make it more realistic
      $shippingCost += rand(-20, 30);
      $shippingCost = max(40, $shippingCost); // Minimum $40

      $rates[] = [
        'warehouse_id' => $warehouse['id'],
        'warehouse_name' => $warehouse['name'],
        'warehouse_address' => $warehouse['address'],
        'distance' => round($distance, 1),
        'distance_unit' => 'miles',
        'shipping_cost' => round($shippingCost, 2),
        'estimated_delivery_days' => ceil($distance / 500) + 2, // Rough estimate
        'service_type' => 'FEDEX_GROUND',
        'is_recommended' => false, // Will be set later for cheapest
      ];
    }

    // Sort by shipping cost (cheapest first)
    usort($rates, function ($a, $b) {
      return $a['shipping_cost'] <=> $b['shipping_cost'];
    });

    // Mark the cheapest as recommended
    if (!empty($rates)) {
      $rates[0]['is_recommended'] = true;
    }

    return $rates;
  }

  /**
   * Get real shipping rates from FedEx API
   */
  private function getRealShippingRates($customerAddress, $items, $warehouses = null)
  {
    if (empty($warehouses)) {
      $warehouses = $this->getWarehousesFromDatabase();
    }

    $rates = [];

    foreach ($warehouses as $warehouse) {
      try {
        $response = Http::withHeaders([
          'Content-Type' => 'application/json',
          'X-locale' => 'en_US',
          'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->post($this->rateEndpoint, [
              'accountNumber' => [
                'value' => $this->accountNumber,
              ],
              'requestedShipment' => [
                'shipper' => [
                  'address' => [
                    // 'streetLines' => [$warehouse['address']],
                    'city' => $warehouse['city'],
                    'stateOrProvinceCode' => $warehouse['state'],
                    'postalCode' => $warehouse['postal_code'],
                    'countryCode' => $warehouse['country_code'] ?? 'US',
                  ],
                ],
                'recipient' => [
                  'address' => [
                    // 'streetLines' => [$customerAddress['address1']],
                    'city' => $customerAddress['city'],
                    'stateOrProvinceCode' => $customerAddress['province_code'],
                    'postalCode' => $customerAddress['zip'],
                    'countryCode' => $customerAddress['country_code'] ?? 'US',
                  ],
                ],
                'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
                'serviceType' => 'FEDEX_GROUND',
                'rateRequestType' => ['LIST', 'ACCOUNT'],
                'requestedPackageLineItems' => $this->formatPackageLineItems($items),
              ],
            ]);

        $requestData = [
          'accountNumber' => [
            'value' => $this->accountNumber,
          ],
          'requestedShipment' => [
            'shipper' => [
              'address' => [
                'city' => $warehouse['city'],
                'stateOrProvinceCode' => $warehouse['state'],
                'postalCode' => $warehouse['postal_code'],
                'countryCode' => $warehouse['country_code'] ?? 'US',
              ],
            ],
            'recipient' => [
              'address' => [
                'city' => $customerAddress['city'],
                'stateOrProvinceCode' => $customerAddress['province_code'],
                'postalCode' => $customerAddress['zip'],
                'countryCode' => $customerAddress['country_code'] ?? 'US',
              ],
            ],
            'pickupType' => 'DROPOFF_AT_FEDEX_LOCATION',
            'serviceType' => 'FEDEX_GROUND',
            'rateRequestType' => ['LIST', 'ACCOUNT'],
            'requestedPackageLineItems' => $this->formatPackageLineItems($items),
          ],
        ];

        $headers = [
          'Content-Type' => 'application/json',
          'X-locale' => 'en_US',
          'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];


        echo json_encode($requestData, JSON_PRETTY_PRINT);

        echo json_encode($headers, JSON_PRETTY_PRINT);
        exit;

        dd($response->body());

        if ($response->successful()) {
          $data = $response->json();

          // Extract rate information from FedEx response
          $rateDetails = $data['output']['rateReplyDetails'][0] ?? null;

          if ($rateDetails) {
            $rates[] = [
              'warehouse_id' => $warehouse['id'],
              'warehouse_name' => $warehouse['name'],
              'warehouse_address' => $warehouse['address'],
              'distance' => null, // FedEx doesn't provide distance
              'distance_unit' => 'miles',
              'shipping_cost' => $rateDetails['ratedShipmentDetails'][0]['totalNetCharge'] ?? 0,
              'estimated_delivery_days' => $rateDetails['operationalDetail']['deliveryDays'] ?? null,
              'service_type' => $rateDetails['serviceType'] ?? 'FEDEX_GROUND',
              'is_recommended' => false,
            ];
          }
        } else {
          Log::error('FedEx API Error for warehouse ' . $warehouse['id'], [
            'response' => $response->body(),
          ]);
        }
      } catch (\Exception $e) {
        Log::error('FedEx API Exception for warehouse ' . $warehouse['id'], [
          'error' => $e->getMessage(),
        ]);
      }
    }

    // Sort by shipping cost
    usort($rates, function ($a, $b) {
      return $a['shipping_cost'] <=> $b['shipping_cost'];
    });

    // Mark cheapest as recommended
    if (!empty($rates)) {
      $rates[0]['is_recommended'] = true;
    }

    return $rates;
  }

  /**
   * Get access token for FedEx API
   */
  private function getAccessToken()
  {
    // Implement token caching logic here
    // For now, make a request to get token

    $response = Http::asForm()->post($this->oauthEndpoint, [
      'grant_type' => 'client_credentials',
      'client_id' => $this->apiKey,
      'client_secret' => $this->apiSecret,
    ]);

    if ($response->successful()) {
      return $response->json()['access_token'];
    }

    throw new \Exception('Failed to get FedEx access token');
  }

  /**
   * Format package items for FedEx API
   */
  private function formatPackageLineItems($items)
  {
    $packages = [];

    foreach ($items as $item) {
      $packages[] = [
        'weight' => [
          'units' => 'LB',
          'value' => $item['weight'] ?? 10,
        ],
        'dimensions' => [
          'length' => $item['length'] ?? 12,
          'width' => $item['width'] ?? 12,
          'height' => $item['height'] ?? 12,
          'units' => 'IN',
        ],
      ];
    }

    return $packages;
  }

  /**
   * Get warehouses from database
   */
  private function getWarehousesFromDatabase()
  {
    $warehouses = \App\Models\Warehouse::active()->ordered()->get();

    return $warehouses->map(function ($warehouse) {
      return [
        'id' => $warehouse->id,
        'name' => $warehouse->name,
        'address' => $warehouse->address,
        'city' => $warehouse->city,
        'state' => $warehouse->state,
        'postal_code' => $warehouse->postal_code,
        'country_code' => $warehouse->country_code,
        'latitude' => $warehouse->latitude ?? 0,
        'longitude' => $warehouse->longitude ?? 0,
      ];
    })->toArray();
  }

  /**
   * Calculate distance between two coordinates (Haversine formula)
   */
  private function calculateDistance($lat1, $lon1, $lat2, $lon2)
  {
    $earthRadius = 3959; // miles

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
      cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
      sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    return $earthRadius * $c;
  }

  /**
   * Calculate total weight of items
   */
  private function getTotalWeight($items)
  {
    $totalWeight = 0;
    foreach ($items as $item) {
      $totalWeight += ($item['weight'] ?? 10) * ($item['quantity'] ?? 1);
    }
    return $totalWeight;
  }

  /**
   * Get single warehouse details
   */
  public function getWarehouse($warehouseId)
  {
    $warehouses = $this->getDefaultWarehouses();

    foreach ($warehouses as $warehouse) {
      if ($warehouse['id'] == $warehouseId) {
        return $warehouse;
      }
    }

    return null;
  }

  /**
   * Get default warehouses from database
   */
  private function getDefaultWarehouses()
  {
    $warehouseIds = Setting::where('key', 'default_warehouse_ids')->first()->value ?? [];
    $warehouses = [];

    foreach ($warehouseIds as $warehouseId) {
      $warehouse = $this->getWarehouseById($warehouseId);
      if ($warehouse) {
        $warehouses[] = $warehouse;
      }
    }

    return $warehouses;
  }

  /**
   * Get warehouse by ID from database
   */
  private function getWarehouseById($warehouseId)
  {
    $warehouse = \App\Models\Warehouse::find($warehouseId);

    if (!$warehouse) {
      return null;
    }

    return [
      'id' => $warehouse->id,
      'name' => $warehouse->name,
      'address' => $warehouse->address,
      'city' => $warehouse->city,
      'state' => $warehouse->state,
      'postal_code' => $warehouse->postal_code,
      'country_code' => $warehouse->country_code,
      'latitude' => $warehouse->latitude ?? 0,
      'longitude' => $warehouse->longitude ?? 0,
    ];
  }

}
