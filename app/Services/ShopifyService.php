<?php

namespace App\Services;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\Coupon;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ShopifyService
{
    protected $shop;
    protected $accessToken;
    protected $apiVersion;

    public function __construct()
    {
        $this->shop        = env('SHOPIFY_SHOP_DOMAIN');
        $this->accessToken = env('SHOPIFY_ACCESS_TOKEN');
        $this->apiVersion  = env('SHOPIFY_API_VERSION');
    }

    private function request($method, $endpoint, $data = null)
    {
        $url = "https://{$this->shop}/admin/api/{$this->apiVersion}/{$endpoint}";

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])
        ->{$method}($url, $data ?? []);

        if ($response->failed()) {
            throw new Exception("Shopify API Error: " . json_encode($response->json()));
        }

        return $response->json();
    }

    public function makeRequest(string $endpoint, string $method = 'GET', array $data = [])
    {
        $endpoint = ltrim($endpoint, '/');
        $url = "https://{$this->shop}/admin/api/{$this->apiVersion}/{$endpoint}";

        $headers = [
            'X-Shopify-Access-Token' => $this->accessToken,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        try {
            // Use Laravel HTTP client
            if (strtoupper($method) === 'GET') {
                $response = Http::withHeaders($headers)->get($url, $data);
            } else {
                $response = Http::withHeaders($headers)->send(strtoupper($method), $url, ['json' => $data]);
            }
        } catch (\Throwable $e) {
            Log::error('Shopify makeRequest exception', ['url' => $url, 'error' => $e->getMessage()]);
            return null;
        }

        // Log non-200 responses for easier debugging
        if ($response->failed()) {
            Log::error('Shopify API failed', [
                'url' => $url,
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return null;
        }

        $json = $response->json();

        return $json;
    }

    public function getStoreCurrency(): string
    {
        $response = $this->makeRequest('shop.json', 'GET');
        return $response['shop']['currency'] ?? 'USD';
    }


    /**This method is used to make GraphQL requests (for discounts) */
    public function makeGraphQLRequest(string $query, array $variables = []): array
    {
        $shopDomain = $this->shop; 
        $accessToken = $this->accessToken;

        if (!$shopDomain || !$accessToken) {
            throw new \Exception('Shopify credentials are missing');
        }

        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->post(
            "https://{$shopDomain}/admin/api/2025-10/graphql.json",
            [
                'query' => $query,
                'variables' => $variables,
            ]
        );

        // Shopify GraphQL always returns 200, even on errors
        if (!$response->ok()) {
            Log::error('Shopify GraphQL HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('Shopify GraphQL request failed');
        }

        $json = $response->json();

        if (isset($json['errors'])) {
            Log::error('Shopify GraphQL errors', [
                'errors' => $json['errors'],
                'query' => $query,
                'variables' => $variables,
            ]);
            throw new \Exception('Shopify GraphQL returned errors');
        }

        return $json;
    }

    // ----------------
    // Generic Methods
    // ----------------
    public function get($endpoint, $params = [])
    {
        $query = $params ? '?' . http_build_query($params) : '';
        return $this->request('get', "{$endpoint}{$query}");
    }

    public function post($endpoint, $data)
    {
        return $this->request('post', $endpoint, $data);
    }

    public function put($endpoint, $data)
    {
        return $this->request('put', $endpoint, $data);
    }

    public function delete($endpoint)
    {
        return $this->request('delete', $endpoint);
    }

    // ----------------
    // Convenience APIs
    // ----------------
    public function getProducts($params = [])
    {
        return $this->get('products.json', $params);
    }

    public function getOrders($params = [])
    {
        return $this->get('orders.json', $params);
    }

    public function getCustomers($params = [])
    {
        return $this->get('customers.json', $params);
    }

    public function createProduct($productData)
    {
        return $this->post('products.json', ['product' => $productData]);
    }
   
    public function syncProductsToDB()
    {
        $pageInfo = null;
            $count = 0;

            do {
                $params = ['limit' => 50];
                if ($pageInfo) $params['page_info'] = $pageInfo;

                $data = $this->getProducts($params);

                foreach ($data['products'] as $shopProduct) {
                    $product = Product::updateOrCreate(['shopify_id' => $shopProduct['id']], [
                        'title' => $shopProduct['title'],
                        'body_html' => $shopProduct['body_html'] ?? null,
                        'vendor' => $shopProduct['vendor'] ?? null,
                        'product_type' => $shopProduct['product_type'] ?? null,
                        'status' => $shopProduct['status'] ?? null,
                        'image' => $shopProduct['image']['src'] ?? null,
                        'price' => $shopProduct['variants'][0]['price'] ?? null,
                    ]);
                    // remove old images before syncing new ones
                    $product->images()->delete(); 
                        foreach ($shopProduct['images'] ?? [] as $img) {
                            $product->images()->create([
                                'src' => $img['src']
                            ]);
                        }

                        foreach ($shopProduct['variants'] ?? [] as $variant) {
                        ProductVariant::updateOrCreate(
                            ['shopify_variant_id' => $variant['id']],
                            [
                                'product_id' => $product->id,
                                'title' => $variant['title'] ?? null,
                                'price' => $variant['price'] ?? null,
                                'sku' => $variant['sku'] ?? null,
                                'inventory_quantity' => $variant['inventory_quantity'] ?? 0,
                            ]
                        );
                    }

                }

                

                $count += count($data['products']);
                $pageInfo = $data['next'] ?? null;

            } while ($pageInfo);

            return $count;
    }

    public function syncOrdersToDB()
    {
        $pageInfo = null;
        $count = 0;

        $toDecimal = function ($value) {
            if (is_null($value) || $value === '') {
                return null;
            }
            
            if (is_numeric($value)) {
                return (float) $value;
            }
            
            if (is_string($value)) {
                // Remove any currency symbols and commas
                $cleaned = preg_replace('/[^\d\.\-]/', '', $value);
                return $cleaned !== '' ? (float) $cleaned : null;
            }
            
            return null;
        };
        


        do {
            $params = ['limit' => 50];
            if ($pageInfo) $params['page_info'] = $pageInfo;

            // Call Shopify API endpoint
            $data = $this->getOrders($params);  

            if (!isset($data['orders']) || empty($data['orders'])) break;

            foreach ($data['orders'] as $orderData) {

                $shippingPrice = null;
                if (isset($orderData['total_shipping_price_set']['shop_money']['amount'])) {
                    $shippingPrice = $toDecimal($orderData['total_shipping_price_set']['shop_money']['amount']);
                } elseif (isset($orderData['shipping_lines'][0]['price'])) {
                    $shippingPrice = $toDecimal($orderData['shipping_lines'][0]['price']);
                }
                
                // Calculate total line items quantity
                $totalQuantity = 0;
                if (isset($orderData['line_items']) && is_array($orderData['line_items'])) {
                    foreach ($orderData['line_items'] as $item) {
                        $totalQuantity += $item['quantity'] ?? 0;
                    }
                }


                \App\Models\Order::updateOrCreate(
                    ['shopify_order_id' => $orderData['id']],
                    [
                       'order_number' => $orderData['order_number'] ?? null,
            
                    // Customer Information
                    'customer_name' => trim(($orderData['customer']['first_name'] ?? '') . ' ' . ($orderData['customer']['last_name'] ?? '')),
                    'customer_email' => $orderData['customer']['email'] ?? null,
                    
                    // Pricing
                    'total_price' => $toDecimal($orderData['total_price'] ?? null),
                    'subtotal_price' => $toDecimal($orderData['subtotal_price'] ?? null),
                    'total_tax' => $toDecimal($orderData['total_tax'] ?? null),
                    'total_discounts' => $toDecimal($orderData['total_discounts'] ?? null),
                    'total_line_items_price' => $toDecimal($orderData['total_line_items_price'] ?? null),
                    'total_price_usd' => $toDecimal($orderData['total_price_usd'] ?? null),
                    'total_shipping_price' => $shippingPrice,
                    'total_refunded' => $toDecimal($orderData['total_refunded'] ?? 0),
                    
                    // Currency
                    'currency' => $orderData['currency'] ?? null,
                    'presentment_currency' => $orderData['presentment_currency'] ?? null,
                    
                    // Status
                    'financial_status' => $orderData['financial_status'] ?? null,
                    'fulfillment_status' => $orderData['fulfillment_status'] ?? null,
                    'processing_method' => $orderData['processing_method'] ?? null,
                    
                    // JSON Data
                    'billing_address' => $orderData['billing_address'] ?? null,
                    'shipping_address' => $orderData['shipping_address'] ?? null,
                    'line_items' => $orderData['line_items'] ?? null,
                    'total_price_set' => $orderData['total_price_set'] ?? null,
                    'subtotal_price_set' => $orderData['subtotal_price_set'] ?? null,
                    'total_discounts_set' => $orderData['total_discounts_set'] ?? null,
                    'total_shipping_price_set' => $orderData['total_shipping_price_set'] ?? null,
                    'total_tax_set' => $orderData['total_tax_set'] ?? null,
                    
                    // Timestamps
                    'shopify_created_at' => isset($orderData['created_at']) ? Carbon::parse($orderData['created_at']) : null,
                    'shopify_updated_at' => isset($orderData['updated_at']) ? Carbon::parse($orderData['updated_at']) : null,
                    'cancelled_at' => isset($orderData['cancelled_at']) ? Carbon::parse($orderData['cancelled_at']) : null,
                    'closed_at' => isset($orderData['closed_at']) ? Carbon::parse($orderData['closed_at']) : null,
                    'synced_at' => now(),
                    ]
                );
            }

            $count += count($data['orders']);
            $pageInfo = $data['next'] ?? null;

        } while ($pageInfo);

        return $count;
    }
    public function getPrimaryLocationId(): ?int
    {
        $resp = $this->makeRequest('locations.json', 'GET');

        if (!$resp || !isset($resp['locations']) || !is_array($resp['locations'])) {
            Log::warning('Shopify locations response missing or invalid', ['resp' => $resp]);
            return null;
        }

        $locations = $resp['locations'];

        if (empty($locations)) {
            Log::warning('Shopify returned empty locations array', ['resp' => $resp]);
            return null;
        }

        // Prefer first active location if present
        foreach ($locations as $loc) {
            if (array_key_exists('active', $loc) && $loc['active']) {
                return (int)$loc['id'];
            }
        }

        // Fallback to the first returned location
        return (int)$locations[0]['id'];
    }
    public function syncCustomersToDB()
    {
        $pageInfo = null;
        $count = 0;

        do {
            $params = ['limit' => 50];
            if ($pageInfo) $params['page_info'] = $pageInfo;

            $data = $this->getCustomers($params);

            foreach ($data['customers'] as $shopCustomer) {
                \App\Models\ShopifyCustomer::updateOrCreate(
                    ['shopify_customer_id' => $shopCustomer['id']],
                    [
                        'first_name' => $shopCustomer['first_name'] ?? null,
                        'last_name' => $shopCustomer['last_name'] ?? null,
                        'email' => $shopCustomer['email'] ?? null,
                        'phone' => $shopCustomer['phone'] ?? null,
                        'verified_email' => $shopCustomer['verified_email'] ?? false,
                        'state' => $shopCustomer['state'] ?? null,
                        'orders_count' => $shopCustomer['orders_count'] ?? 0,
                        'total_spent' => $shopCustomer['total_spent'] ?? 0,
                        'currency' => $shopCustomer['currency'] ?? null,
                        'accepts_marketing' => $shopCustomer['accepts_marketing'] ?? false,
                        'addresses' => $shopCustomer['addresses'] ?? null,
                        'default_address' => $shopCustomer['default_address'] ?? null,
                        'raw_response' => $shopCustomer,
                    ]
                );
            }

            $count += count($data['customers']);
            $pageInfo = $data['next'] ?? null;

        } while ($pageInfo);

        return $count;
    }

    public function createDraftOrder(array $payload)
    {
        return $this->makeRequest(
            'draft_orders.json',
            'POST',
            ['draft_order' => $payload]
        );
    }

    public function completeDraftOrder(int $draftOrderId)
    {
        return $this->makeRequest(
            "draft_orders/{$draftOrderId}/complete.json",
            'PUT',
            ['payment_pending' => true] // unpaid order
        );
    }

    public function updateCustomer(int $shopifyCustomerId, array $payload)
    {
        return $this->makeRequest(
            "customers/{$shopifyCustomerId}.json",
            'PUT',
            [
                'customer' => $payload
            ]
        );
    }
    public function deleteCustomer(int $shopifyCustomerId)
    {
        return $this->makeRequest(
        "customers/{$shopifyCustomerId}/redact.json",
        'POST',
        [
            'customer' => [
                'id' => $shopifyCustomerId,
            ]
        ]
    );
    }


    public function cancelOrder( int $shopifyOrderId,bool $restock = true,string $reason = 'customer') {
        return $this->makeRequest(
            "orders/{$shopifyOrderId}/cancel.json",
            'POST',
            [
                'restock' => $restock,
                'reason' => $reason,
                'notify_customer' => true,
            ]
        );
    }

    public function restockOnly(int $shopifyOrderId, array $refundLineItems)
    {
        return $this->makeRequest(
            "orders/{$shopifyOrderId}/refunds.json",
            'POST',
            [
                'refund' => [
                    'refund_line_items' => $refundLineItems,
                    'restock' => true,
                    'notify' => true,
                ]
            ]
        );
    }

    public function refundPaidOrder(int $shopifyOrderId, float $amount, string $currency, array $lineItems = [])
    {
        try {
            // First, get the order details to understand the structure
            $orderResponse = $this->makeRequest(
                "orders/{$shopifyOrderId}.json",
                'GET'
            );
            
            $order = $orderResponse['order'] ?? [];
            
            if (empty($order)) {
                throw new \Exception('Order not found');
            }
            
            // Get refundable transactions
            $parentTransaction = $this->getRefundableTransaction($shopifyOrderId);
            
            if (!$parentTransaction) {
                throw new \Exception('Order has no refundable transaction');
            }
            
            // Prepare refund data
            $refundData = [
                'refund' => [
                    'currency' => $currency,
                    'notify' => true,
                    'note' => 'Refund processed from Laravel app',
                    'shipping' => [
                        'full_refund' => true
                    ],
                    'transactions' => [
                        [
                            'parent_id' => $parentTransaction['id'],
                            'kind' => 'refund',
                            'amount' => number_format($amount, 2, '.', ''),
                            'gateway' => $parentTransaction['gateway'] ?? null,
                        ]
                    ]
                ]
            ];
            
            // If line items are provided, refund specific items
            if (!empty($lineItems)) {
                $refundData['refund']['refund_line_items'] = $lineItems;
            } else {
                // Full refund - refund all line items
                $refundLineItems = [];
                foreach ($order['line_items'] as $item) {
                    $refundLineItems[] = [
                        'line_item_id' => $item['id'],
                        'quantity' => $item['quantity'],
                        'location_id' => $item['location_id'] ?? null,
                    ];
                }
                $refundData['refund']['refund_line_items'] = $refundLineItems;
            }
            
            // Make the refund request
            $response = $this->makeRequest(
                "orders/{$shopifyOrderId}/refunds.json",
                'POST',
                $refundData
            );
            
            return $response;
            
        } catch (\Exception $e) {
            \Log::error('Refund failed', [
                'shopify_order_id' => $shopifyOrderId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function refundFullOrder(int $shopifyOrderId, string $currency = 'INR')
    {
        try {
            // Get order details
            $orderResponse = $this->makeRequest(
                "orders/{$shopifyOrderId}.json",
                'GET',
                ['fields' => 'id,line_items,location_id,fulfillment_status,total_price,total_shipping_price_set,transactions']
            );
            
            $order = $orderResponse['order'] ?? [];
            
            if (empty($order)) {
                throw new \Exception('Order not found');
            }
            
            // Get a location ID from Shopify
            $locationId = $this->getPrimaryLocationId();
            
            if (!$locationId) {
                throw new \Exception('No location found in Shopify store');
            }
            
            // Get parent transaction
            $parentTransaction = $this->getRefundableTransaction($shopifyOrderId);
            
            if (!$parentTransaction) {
                throw new \Exception('Order has no refundable transaction');
            }
            
            // Prepare refund line items with location_id (location_id is required for restocking)
            $refundLineItems = [];
            foreach ($order['line_items'] as $item) {
                $refundLineItems[] = [
                    'line_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'restock_type' => 'cancel', 
                    'location_id' => $locationId, 
                ];
            }
            
            // Get shipping amount
            $shippingAmount = 0;
            if (isset($order['total_shipping_price_set']['shop_money']['amount'])) {
                $shippingAmount = $order['total_shipping_price_set']['shop_money']['amount'];
            }
            
            // Prepare refund data
            $refundData = [
                'refund' => [
                    'currency' => $currency,
                    'notify' => true,
                    'note' => 'Full refund processed from Laravel app',
                    'shipping' => [
                        'amount' => $shippingAmount,
                        'full_refund' => true,
                    ],
                    'refund_line_items' => $refundLineItems,
                    'transactions' => [
                        [
                            'parent_id' => $parentTransaction['id'],
                            'kind' => 'refund',
                            'amount' => $order['total_price'],
                            'gateway' => $parentTransaction['gateway'] ?? null,
                            'currency' => $currency,
                        ]
                    ]
                ]
            ];
            
            return $this->makeRequest(
                "orders/{$shopifyOrderId}/refunds.json",
                'POST',
                $refundData
            );
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function refundFulfilledOrder(int $shopifyOrderId, string $currency = 'INR')
    {
        try {
            $orderResponse = $this->makeRequest(
                "orders/{$shopifyOrderId}.json",
                'GET'
            );
            
            $order = $orderResponse['order'] ?? [];
            
            if (empty($order)) {
                throw new \Exception('Order not found');
            }
            
            $parentTransaction = $this->getRefundableTransaction($shopifyOrderId);
            
            if (!$parentTransaction) {
                throw new \Exception('Order has no refundable transaction');
            }
            
            // Get fulfillment location
            $locationId = null;
            if (!empty($order['fulfillments'])) {
                $locationId = $order['fulfillments'][0]['location_id'] ?? null;
            }
            
            if (!$locationId) {
                throw new \Exception('Unable to determine fulfillment location');
            }
            
            // Calculate subtotal for line items (excluding taxes and shipping)
            $subtotal = 0;
            $refundLineItems = [];
            
            foreach ($order['line_items'] as $item) {
                $subtotal += ($item['price'] * $item['quantity']);
                $refundLineItems[] = [
                    'line_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'restock_type' => 'no_restock', 
                    'location_id' => $locationId, 
                ];
            }
            
            // Calculate shipping amount to refund
            $shippingAmount = (float) ($order['total_shipping_price_set']['presentment_money']['amount'] ?? 0);
            
            $refundData = [
                'refund' => [
                    'currency' => $currency,
                    'notify' => true,
                    'note' => 'Refund for fulfilled order - items not restocked',
                    'shipping' => [
                        'amount' => (string) $shippingAmount, 
                    ],
                    'refund_line_items' => $refundLineItems,
                    'transactions' => [
                        [
                            'parent_id' => $parentTransaction['id'],
                            'kind' => 'refund',
                            'amount' => (string) ($subtotal + $shippingAmount), // Subtotal + shipping
                            'gateway' => $parentTransaction['gateway'] ?? null,
                            'currency' => $currency,
                        ]
                    ]
                ]
            ];
            
            // Optional: Include tax refund if applicable
            if (!empty($order['total_tax'])) {
                $refundData['refund']['transactions'][0]['amount'] = (string) $order['total_price'];
            }
            
            return $this->makeRequest(
                "orders/{$shopifyOrderId}/refunds.json",
                'POST',
                $refundData
            );
            
        } catch (\Exception $e) {
            \Log::error('Fulfilled order refund failed', [
                'shopify_order_id' => $shopifyOrderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString() // Add trace for debugging
            ]);
            throw $e;
        }
    }


    private function getRefundableTransaction(int $shopifyOrderId)
    {
        try {
            // Get transactions for the order
            $response = $this->makeRequest(
                "orders/{$shopifyOrderId}/transactions.json",
                'GET'
            );
            
            $transactions = $response['transactions'] ?? [];
            
            // Find a successful charge transaction
            foreach ($transactions as $transaction) {
                if ($transaction['status'] === 'success' && 
                    $transaction['kind'] === 'sale' && 
                    $transaction['amount'] > 0) {
                    return $transaction;
                }
            }
            
            return null;
            
        } catch (\Exception $e) {
            \Log::error('Failed to get refundable transaction', [
                'shopify_order_id' => $shopifyOrderId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function getLocations()
    {
        $res = $this->makeRequest('locations.json', 'GET');
        return $res['locations'] ?? [];
    }

    public function refundPartialOrder(int $shopifyOrderId, array $items,string $currency = 'INR',float $refundShippingAmount = 0.00, float $taxAdjustment = 0.00) 
    {
        try {
            // 1️⃣ Get order details
            $orderResponse = $this->makeRequest(
                "orders/{$shopifyOrderId}.json",
                'GET',
                ['fields' => 'id,line_items,total_price,total_shipping_price_set']
            );

            $order = $orderResponse['order'] ?? [];

            if (empty($order)) {
                throw new \Exception('Order not found');
            }

            // 2️⃣ Get primary location
            $locationId = $this->getPrimaryLocationId();

            if (!$locationId) {
                throw new \Exception('No location found in Shopify store');
            }

            // 3️⃣ Get parent transaction
            $parentTransaction = $this->getRefundableTransaction($shopifyOrderId);

            if (!$parentTransaction) {
                throw new \Exception('Order has no refundable transaction');
            }

            // 4️⃣ Build refund line items + calculate amount
            $refundLineItems = [];
            $refundAmount = 0;

            foreach ($items as $refundItem) {
                $lineItem = collect($order['line_items'])
                    ->firstWhere('id', $refundItem['line_item_id']);

                if (!$lineItem) {
                    continue;
                }

                $qty = min(
                    (int) $refundItem['quantity'],
                    (int) $lineItem['quantity']
                );

                if ($qty <= 0) {
                    continue;
                }

                $refundLineItems[] = [
                    'line_item_id' => $lineItem['id'],
                    'quantity' => $qty,
                    'restock_type' => 'return',
                    'location_id' => $locationId,
                ];

                $refundAmount += ((float) $lineItem['price'] * $qty);
            }

            if (empty($refundLineItems)) {
                throw new \Exception('No valid line items selected for refund');
            }

            // 5️⃣ Add shipping refund if requested
            if ($refundShippingAmount > 0) {
                $refundAmount += $refundShippingAmount;
            }

            // 6️⃣ Prepare refund payload
            $refundData = [
                'refund' => [
                    'currency' => $currency,
                    'notify' => true,
                    'note' => 'Partial refund processed from Laravel app',
                    'refund_line_items' => $refundLineItems,
                    'transactions' => [
                        [
                            'parent_id' => $parentTransaction['id'],
                            'kind' => 'refund',
                            'amount' => number_format($refundAmount, 2, '.', ''),
                            'gateway' => $parentTransaction['gateway'] ?? null,
                            'currency' => $currency,
                        ]
                    ]
                ]
            ];

            // 7️⃣ Optional shipping refund
            if ($refundShippingAmount > 0) {
                $refundData['refund']['shipping'] = [
                    'amount' => number_format($refundShippingAmount, 2, '.', ''),
                ];
            }

            // 8️⃣ Send refund request
            return $this->makeRequest(
                "orders/{$shopifyOrderId}/refunds.json",
                'POST',
                $refundData
            );

        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function updateDraftOrder(int $draftOrderId, array $data): array|null
    {
        return $this->makeRequest(
            "draft_orders/{$draftOrderId}.json",
            'PUT',
            [
                'draft_order' => $data
            ]
        );
    }

    public function updateOrderRest($shopifyOrderId)
    {
        $payload = [
            "order" => [
                "id" => $shopifyOrderId,
                "note" => "Updated via Laravel App",
                // You can add tags or customer details here
            ]
        ];

        $response = Http::withHeaders(['X-Shopify-Access-Token' => $this->accessToken])
            ->put("https://{$this->shop}/admin/api/{$this->apiVersion}/orders/{$shopifyOrderId}.json", $payload);

        return $response->json();
    }

    /**
     * To remove an item in REST, you must "Refund" it.
     */
    public function refundItemRest($shopifyOrderId, $lineItemId, $quantity)
    {
        

        $payload = [
            "refund" => [
                "currency" => "USD",
                "notify" => false,
                "refund_line_items" => [
                    [
                        "line_item_id" => $lineItemId,
                        "quantity" => $quantity,
                        "restock_type" => "no_restock"
                    ]
                ]
            ]
        ];

        return Http::withHeaders(['X-Shopify-Access-Token' => $this->accessToken])
            ->post("https://{$this->shop}/admin/api/{$this->apiVersion}/orders/{$shopifyOrderId}/refunds.json", $payload)
            ->json();
    }


    public function syncCouponsToDB(): int
    {
        $syncedCount = 0;
        $priceRulesResponse = $this->makeRequest('price_rules.json', 'GET');
        $priceRules = $priceRulesResponse['price_rules'] ?? [];
        $discounts = $this->getAllDiscountsGraphQL();
        foreach ($priceRules as $rule) {
            $codesResponse = $this->makeRequest(
                "price_rules/{$rule['id']}/discount_codes.json",
                'GET'
            );

            $discountCodes = $codesResponse['discount_codes'] ?? [];

            foreach ($discountCodes as $code) {

                Coupon::updateOrCreate(
                    [
                        'shopify_price_rule_id' => $rule['id'],
                        'code' => $code['code'],
                    ],
                    [
                        'shopify_discount_code_id' => $code['id'],
                        'title' => $rule['title'] ?? null,
                        'value_type' => $rule['value_type'] === 'percentage' ? 'percentage' : 'fixed_amount',
                        'value' => abs((float) $rule['value']), 
                        'target_type' => $rule['target_type'] ?? null,
                        'target_selection' => $rule['target_selection'] ?? null,
                        'allocation_method' => $rule['allocation_method'] ?? null,
                        'usage_limit' => $rule['usage_limit'] ?? null,
                        'times_used' => $code['usage_count'] ?? 0,
                        'customer_selection' => $rule['customer_selection'] ?? null,
                        'once_per_customer' => (bool) ($rule['once_per_customer'] ?? false),
                        'starts_at' => isset($rule['starts_at']) ? Carbon::parse($rule['starts_at']) : null,
                        'ends_at' => isset($rule['ends_at']) ? Carbon::parse($rule['ends_at']) : null,
                        'status' => Coupon::determineStatus($rule),
                        'raw_price_rule' => $rule,
                        'raw_discount_code' => $code,
                        'synced_at' => now(),
                    ]
                );

                $syncedCount++;
            }
        }

        if (!empty($shopifyCodes)) {
            Coupon::where('source', 'graphql')
                ->whereNotIn('code', $shopifyCodes)
                ->delete();
        }

        return $syncedCount;
    }


    public function deletePriceRule(int $priceRuleId): bool
    {
        $response = Http::withHeaders([
            'X-Shopify-Access-Token' => $this->accessToken,
            'Content-Type' => 'application/json',
        ])
        ->delete(
            "https://{$this->shop}/admin/api/{$this->apiVersion}/price_rules/{$priceRuleId}.json"
        );

        if ($response->status() === 200 || $response->status() === 204) {
            return true;
        }

        \Log::error('Failed to delete price rule', [
            'price_rule_id' => $priceRuleId,
            'status' => $response->status(),
            'body' => $response->body(),
        ]);

        return false;
    }


    public function createRestCoupon($data)
    {
        // 1. Create the Price Rule
        $priceRulePayload = [
            "price_rule" => [
                "title" => $data['title'],
                "target_type" => $data['target_type'],
                "target_selection" => $data['target_selection'],
                "allocation_method" => $data['allocation_method'],
                "value_type" => $data['value_type'],
                "value" => $data['value_type'] == 'percentage' ? "-" . abs($data['value']) : "-" . abs($data['value']),
                "customer_selection" => $data['customer_selection'],
                "starts_at" => date('c', strtotime($data['starts_at'])),
                "ends_at" => $data['ends_at'] ? date('c', strtotime($data['ends_at'])) : null,
                "usage_limit" => $data['usage_limit'] ?? null,
                "once_per_customer" => isset($data['once_per_customer']) ? true : false,
            ]
        ];

        $ruleResponse = $this->restRequest('POST', 'price_rules.json', $priceRulePayload);

        if (isset($ruleResponse['errors']) || !isset($ruleResponse['price_rule'])) {
            return ['success' => false, 'error' => $ruleResponse];
        }

        $priceRuleId = $ruleResponse['price_rule']['id'];

        // 2. Create the Discount Code for that Rule
        $discountPayload = [
            "discount_code" => [
                "code" => $data['code']
            ]
        ];

        $codeResponse = $this->restRequest('POST', "price_rules/{$priceRuleId}/discount_codes.json", $discountPayload);

        return [
            'success' => true,
            'price_rule' => $ruleResponse['price_rule'],
            'discount_code' => $codeResponse['discount_code'] ?? null
        ];
    }

    // Helper for REST Requests
    public function restRequest($method, $endpoint, $payload = [])
    {
        $url = "https://{$this->shop}/admin/api/2024-01/{$endpoint}";
        $response = Http::withHeaders(['X-Shopify-Access-Token' => $this->accessToken])
                        ->{strtolower($method)}($url, $payload);
        return $response->json();
    }


}