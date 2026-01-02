{{-- Order Detail Offcanvas --}}
<x-offcanvas id="orderDetailOffcanvas" title="Order Details" position="end">
  <x-slot:body>

    @if (!$ticket->order)
      <div class="alert alert-warning">
        No order associated with this ticket.
      </div>
    @else
      @php
        $order = $ticket->order;
        $orderDate = isset($order->shopify_created_at)
            ? \Carbon\Carbon::parse($order->shopify_created_at)->format('m/d/Y')
            : 'N/A';
        $totalPrice = $order->total_price ?? '0.00';
        $financialStatus = $order->financial_status ?? 'pending';
        $fulfillmentStatus = $order->fulfillment_status ?? 'unfulfilled';
        $lineItems = $order->line_items ?? [];
        $shippingAddress = $order->shipping_address ?? [];
        $shippingLines = $order->shipping_lines ?? [];
      @endphp

      <div class="card grey-g2 px-5 shadow-sm">
        <div class="card-header border-0 px-0  pb-4 border-bottom border-dashed">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 fs-20 primary-black mb-2">Order number: <span
                class="fw-semibold text-success-dark">{{ $order->order_number ?? 'N/A' }}</span>
            </h5>
            <div class="badges">
              <span
                class="badge {{ $order->financial_badge_class }}">{{ ucfirst(str_replace('_', ' ', $financialStatus)) }}</span>
              <span
                class="badge {{ $order->fulfillment_badge_class }}">{{ ucfirst(str_replace('_', ' ', $fulfillmentStatus)) }}</span>
            </div>
          </div>
        </div>
        <div class="card-body order-more-details py-5 px-0 border-0 border-bottom border-dashed">
          <div class="d-flex flex-column gap-2">
            <div class="d-flex align-items-center">
              <img src="{{ asset('assets/img/customizer/email.svg') }}" class="w-7 h-7 me-2" alt="Email" />
              <div>
                <span class="">Ordered on:</span>
                <span class="primary-black">{{ $orderDate }}</span>
              </div>
            </div>
            <div class="d-flex align-items-center">
              <img src="{{ asset('assets/img/customizer/phone.svg') }}" class="me-2 w-7 h-7" alt="Phone" />
              <div>
                <span>Total:</span>
                <span class="primary-black">${{ number_format($totalPrice, 2) }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Shipping Address -->
        @if (!empty($shippingAddress))
          <div class="py-5 px-0 border-0 border-bottom border-dashed">
            <h6 class="fw-semibold mb-3 primary-black">Shipping Address</h6>
            <div class="d-flex flex-column gap-1 text-sm">
              @if (!empty($shippingAddress['name']))
                <div>
                  <span class="text-muted">Name:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['name'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['address1']))
                <div>
                  <span class="text-muted">Address1:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['address1'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['address2']))
                <div>
                  <span class="text-muted">Address2:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['address2'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['city']))
                <div>
                  <span class="text-muted">City:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['city'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['country']))
                <div>
                  <span class="text-muted">Country:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['country'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['province']))
                <div>
                  <span class="text-muted">Province:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['province'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['province_code']))
                <div>
                  <span class="text-muted">Province code:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['province_code'] }}</span>
                </div>
              @endif
              @if (!empty($shippingAddress['zip']))
                <div>
                  <span class="text-muted">Zip:</span>
                  <span class="ms-2 primary-black">{{ $shippingAddress['zip'] }}</span>
                </div>
              @endif
            </div>
          </div>
        @endif

        <!-- Line Items -->
        @if (!empty($lineItems))
          <div class="py-5 px-0 border-0 border-bottom border-dashed">
            <h6 class="fw-semibold mb-3 primary-black">Order Items</h6>
            <div class="d-flex flex-column gap-3">
              @foreach ($lineItems as $itemIndex => $item)
                <div class="d-flex gap-2" wire:key="order-detail-item-{{ $itemIndex }}">
                  <div class="flex-grow-1">
                    <div class="primary-black">
                      <span>{{ $item['quantity'] ?? 1 }}×</span>
                      <span>{{ $item['name'] ?? ($item['title'] ?? 'Unknown Product') }}</span>
                    </div>
                    <div class="text-sm text-muted mt-1">
                      <div>
                        <span class="text-muted">Amount:</span>
                        <span class="ms-2 primary-black">${{ number_format($item['price'] ?? 0, 2) }}</span>
                      </div>
                      @if (!empty($item['sku']))
                        <div>
                          <span class="text-muted">SKU:</span>
                          <span class="ms-2 primary-black">{{ $item['sku'] }}</span>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif

        <!-- Shipping Cost -->
        @if (!empty($shippingLines))
          <div class="py-5 px-0 border-0 border-bottom border-dashed">
            <h6 class="fw-semibold mb-3 primary-black">Shipping Cost</h6>
            @foreach ($shippingLines as $shipIndex => $shipping)
              <div class="d-flex flex-column gap-1 text-sm" wire:key="order-detail-ship-{{ $shipIndex }}">
                <div>
                  <span class="text-muted">Amount:</span>
                  <span class="ms-2 primary-black fw-medium">${{ number_format($shipping['price'] ?? 0, 2) }}
                    {{ $order->currency ?? 'USD' }}</span>
                </div>
                @if (!empty($shipping['title']))
                  <div>
                    <span class="text-muted">Code:</span>
                    <span class="ms-2 primary-black">{{ $shipping['title'] }}</span>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @endif

        <!-- Shipment Tracking -->
        @php
          $fulfillments = $order['fulfillments'] ?? [];
        @endphp
        @if (!empty($fulfillments))
          <div class="py-5 px-0">
            <h6 class="fw-semibold mb-3 primary-black">Shipment</h6>
            @foreach ($fulfillments as $fulfillIndex => $fulfillment)
              @php
                $trackingInfo = $fulfillment['tracking_info'] ?? [];
                $shipmentStatus = $fulfillment['shipment_status'] ?? 'pending';
                $statusBadgeClass = match ($shipmentStatus) {
                    'delivered' => 'bg-success-subtle text-success',
                    'in_transit' => 'bg-warning-subtle text-warning',
                    'out_for_delivery' => 'bg-info-subtle text-info',
                    default => 'bg-secondary-subtle text-secondary',
                };
              @endphp
              <div class="d-flex flex-column gap-2 mb-2" wire:key="order-detail-fulfill-{{ $fulfillIndex }}">
                <div>
                  <span
                    class="badge {{ $statusBadgeClass }}">{{ ucfirst(str_replace('_', ' ', $shipmentStatus)) }}</span>
                </div>
                <div class="text-sm">
                  @if (!empty($trackingInfo['number'] ?? $trackingInfo['tracking_number']))
                    <div>
                      <span class="text-muted">Tracking number:</span>
                      <span
                        class="ms-2 primary-black">{{ $trackingInfo['number'] ?? $trackingInfo['tracking_number'] }}</span>
                    </div>
                  @endif
                  @if (!empty($trackingInfo['url'] ?? $trackingInfo['tracking_url']))
                    <div class="mt-1">
                      <span class="text-muted">Tracking URL:</span>
                      <a href="{{ $trackingInfo['url'] ?? $trackingInfo['tracking_url'] }}" target="_blank"
                        class="ms-2 primary-black text-primary text-break">
                        {{ Str::limit($trackingInfo['url'] ?? $trackingInfo['tracking_url'], 50) }}
                      </a>
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="py-5 px-0 border-0 border-bottom border-dashed">
            <h6 class="fw-semibold mb-3 primary-black">Shipment</h6>
            <div class="text-sm text-muted fst-italic">
              No shipment information available yet
            </div>
          </div>
        @endif
      </div>
    @endif
  </x-slot:body>
</x-offcanvas>
