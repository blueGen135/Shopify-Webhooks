<?php

namespace App\Services;

use App\Helpers\Helpers;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OdooService
{
  /**
   * Get inventory information for a product by SKU
   * Calls the actual Odoo API
   * 
   * @param string $sku
   * @return array
   * @throws \Exception
   */
  public function getInventoryBySku(string $sku): array
  {
    if (config('app.env') === 'local' || config('app.env') === 'testing') {
      // Return dummy data in local or testing environments
      return $this->getDummyInventoryData($sku);
    }

    // Get Odoo settings
    $endpoint = Helpers::setting('odoo_endpoint');
    $customerNumber = Helpers::setting('odoo_customer_number');
    $password = Helpers::setting('odoo_password');
    $companiesString = Helpers::setting('odoo_companies');

    // If settings are not configured, throw exception
    if (empty($endpoint) || empty($customerNumber) || empty($password)) {
      throw new \Exception('Odoo API settings not configured. Please configure endpoint, customer number, and password in settings.');
    }

    // Parse companies from comma-separated string to array
    $companies = !empty($companiesString)
      ? array_map('trim', explode(',', $companiesString))
      : [];

    // Prepare API request
    $requestData = [
      'Customer_number' => $customerNumber,
      'Password' => $password,
      'product_id' => $sku,
      'Companies' => $companies
    ];

    // Make API call
    $response = Http::timeout(30)->post($endpoint, $requestData);

    if (!$response->successful()) {
      Log::error('Odoo API request failed', [
        'status' => $response->status(),
        'body' => $response->body()
      ]);
      throw new \Exception('Odoo API request failed with status: ' . $response->status());
    }

    $data = $response->json();

    // Extract result from jsonrpc wrapper
    if (isset($data['result']) && is_array($data['result']) && count($data['result']) > 0) {
      return $data['result'][0];
    }

    Log::warning('Odoo API returned unexpected format', ['data' => $data]);
    throw new \Exception('Odoo API returned unexpected response format.');
  }

  /**
   * Get dummy inventory data for testing
   * Returns data in the exact format that Odoo API provides
   * 
   * @param string $sku
   * @return array
   */
  private function getDummyInventoryData(string $sku): array
  {
    $data = json_decode('{
        "jsonrpc": "2.0",
        "id": null,
        "result": [
            {
                "sku": "1200000090",
                "companies": [
                    {
                        "company_code": "1485",
                        "warehouses": [
                            {
                                "warehouse_code": "1485",
                                "warehouse_name": "1485 - UTC TS",
                                "available_qty": 100.0,
                                "inbound_shipments": [
                                    {
                                        "receipt_id": "1485/IN/00621",
                                        "eta_date": "2025-11-08",
                                        "quantity": 26.0
                                    },
                                    {
                                        "receipt_id": "1485/IN/00659",
                                        "eta_date": "2025-11-22",
                                        "quantity": 19.0
                                    },
                                    {
                                        "receipt_id": "1485/IN/00664",
                                        "eta_date": "2025-11-25",
                                        "quantity": 28.0
                                    },
                                    {
                                        "receipt_id": "1485/IN/00670",
                                        "eta_date": "2025-12-15",
                                        "quantity": 20.0
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "company_code": "1490",
                        "warehouses": [
                            {
                                "warehouse_code": "1491",
                                "warehouse_name": "UTC - Crumlin (1491)",
                                "available_qty": 100.0,
                                "inbound_shipments": [
                                    {
                                        "receipt_id": "1491/IN/05769",
                                        "eta_date": "2025-11-20",
                                        "quantity": 50.0
                                    },
                                    {
                                        "receipt_id": "1491/IN/05786",
                                        "eta_date": "2025-11-05",
                                        "quantity": 94.0
                                    },
                                    {
                                        "receipt_id": "1491/IN/05803",
                                        "eta_date": "2025-11-08",
                                        "quantity": 48.0
                                    },
                                    {
                                        "receipt_id": "1491/IN/05837",
                                        "eta_date": "2025-11-15",
                                        "quantity": 50.0
                                    },
                                    {
                                        "receipt_id": "1491/IN/05873",
                                        "eta_date": "2025-11-04",
                                        "quantity": 74.0
                                    },
                                    {
                                        "receipt_id": "1491/IN/05906",
                                        "eta_date": "2025-11-22",
                                        "quantity": 72.0
                                    }
                                ]
                            },
                            {
                                "warehouse_code": "1494",
                                "warehouse_name": "UTC - Drummond (1494)",
                                "available_qty": 0.0,
                                "inbound_shipments": [
                                    {
                                        "receipt_id": "1494/IN/00380",
                                        "eta_date": "2026-01-31",
                                        "quantity": 75.0
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        "company_code": "1520",
                        "warehouses": [
                            {
                                "warehouse_code": "1520",
                                "warehouse_name": "1520 - TS Miami",
                                "available_qty": 10.0,
                                "inbound_shipments": [
                                    {
                                        "receipt_id": "1520/IN/00641",
                                        "eta_date": "2025-09-20",
                                        "quantity": 8.0
                                    }
                                ]
                            },
                            {
                                "warehouse_code": "1525",
                                "warehouse_name": "1525 - TS PA",
                                "available_qty": 18.0,
                                "inbound_shipments": [
                                    {
                                        "receipt_id": "1525/IN/01015",
                                        "eta_date": "2025-11-02",
                                        "quantity": 8.0
                                    }
                                ]
                            },
                            {
                                "warehouse_code": "1527",
                                "warehouse_name": "1527 - TS OR",
                                "available_qty": 30.0,
                                "inbound_shipments": [
                                    {
                                        "receipt_id": "1527/IN/00147",
                                        "eta_date": "2025-10-30",
                                        "quantity": 4.0
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ]
    }', true);

    return $data['result'][0];
  }

  /**
   * Get aggregated inventory summary for a product
   * Processes the raw API response into a usable format
   * 
   * @param string $sku
   * @return array
   */
  public function getInventorySummary(string $sku): array
  {
    // Get raw API data
    $apiResponse = $this->getInventoryBySku($sku);

    $totalAvailable = 0;
    $earliestRestock = null;
    $warehouseCount = 0;

    // Process the API response
    foreach ($apiResponse['companies'] as $company) {
      foreach ($company['warehouses'] as $warehouse) {
        $warehouseCount++;
        $availableQty = $warehouse['available_qty'];
        $totalAvailable += $availableQty;

        if (!empty($warehouse['inbound_shipments'])) {
          foreach ($warehouse['inbound_shipments'] as $shipment) {
            if (!$earliestRestock || $shipment['eta_date'] < $earliestRestock) {
              $earliestRestock = $shipment['eta_date'];
            }
          }
        }
      }
    }

    // Return processed summary with raw API data
    return [
      'sku' => $sku,
      'total_available' => $totalAvailable,
      'earliest_restock_date' => $earliestRestock,
      'warehouse_count' => $warehouseCount,
      'raw_data' => $apiResponse
    ];
  }
}
