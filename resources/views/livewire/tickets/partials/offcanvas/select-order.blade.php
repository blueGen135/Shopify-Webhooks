{{-- ticketOrderSelection - ticket-detail.js --}}
<x-offcanvas id="selectOrderOffcanvas" title="Order history" x-data="ticketOrderSelection()">

  <x-slot:body>
    @if ($customerOrders->isEmpty())
      <div class="alert alert-info">
        No orders found for this customer.
      </div>
    @else
      <!-- Search Input -->
      <div class="mb-3">
        <input type="search" class="form-control" placeholder="Search by order number, date, or amount..."
          x-model="searchQuery" x-on:input="filterOrders">
      </div>

      <div class="d-flex flex-column gap-3">
        @foreach ($customerOrders as $index => $order)
          @php
            $orderId = 'order' . $order->id;
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

          <div class="card shadow-sm order-card" wire:key="order-{{ $order->id }}"
            data-order-number="{{ $order->order_number ?? 'N/A' }}" data-order-date="{{ $orderDate }}"
            data-order-total="{{ number_format($totalPrice, 2) }}">
            <div class="card-header border-0 border-bottom border-dashed">
              <div class="d-flex justify-content-between align-items-center">
                <h4 class="m-0">Order number: <span
                    class="text-success-dark">{{ $order->order_number ?? 'N/A' }}</span></h4>
                <div class="badges">
                  <span
                    class="badge {{ $order->financial_badge_class }}">{{ ucfirst(str_replace('_', ' ', $financialStatus)) }}</span>
                  <span
                    class="badge {{ $order->fulfillment_badge_class }}">{{ ucfirst(str_replace('_', ' ', $fulfillmentStatus)) }}</span>
                </div>
              </div>
            </div>
            <div class="card-body py-5 border-0 border-bottom border-dashed">
              <div class="d-flex flex-column gap-2">
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm flex-shrink-0 me-2">
                    <span class="avatar avatar-sm flex-shrink-0 me-2 d-flex align-items-center justify-content-center">
                      <img src="{{ asset('assets/img/customizer/email.svg') }}" class="w-7 h-7" alt="Email" />
                    </span>
                  </div>
                  <div>
                    <span class="">Ordered on:</span>
                    <span>{{ $orderDate }}</span>
                  </div>
                </div>
                <div class="d-flex align-items-center">
                  <div class="avatar avatar-sm flex-shrink-0 me-2">
                    <span class="avatar avatar-sm flex-shrink-0 me-2 d-flex align-items-center justify-content-center">
                      <img src="{{ asset('assets/img/customizer/phone.svg') }}" class="w-7 h-7" alt="Phone" />
                    </span>
                  </div>
                  <div>
                    <span>Total:</span>
                    <span>${{ number_format($totalPrice, 2) }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer py-5">
              <div class="d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-dark" wire:click="selectOrder({{ $order->id }})"
                  wire:loading.attr="disabled">
                  <span wire:loading.remove wire:target="selectOrder({{ $order->id }})">Select</span>
                  <span wire:loading wire:target="selectOrder({{ $order->id }})">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Selecting...
                  </span>
                </button>
                <a href="javascript:void(0);" class="text-dark fw-semi-bold"
                  @click="expandedOrder = expandedOrder === '{{ $orderId }}' ? null : '{{ $orderId }}'">
                  <span x-text="expandedOrder === '{{ $orderId }}' ? 'Less details' : 'More details'"></span>
                  <i class="icon-base ti text-success"
                    :class="expandedOrder === '{{ $orderId }}' ? 'tabler-chevron-up' : 'tabler-chevron-right'"></i>
                </a>
              </div>
            </div>

            <!-- Expandable Details Section -->
            <div x-show="expandedOrder === '{{ $orderId }}'" x-collapse class="border-0 border-top border-dashed">

              <!-- Shipping Address -->
              @if (!empty($shippingAddress))
                <div class="p-4 border-0 border-bottom border-dashed">
                  <h6 class="fw-semibold mb-3">Shipping Address</h6>
                  <div class="d-flex flex-column gap-1 text-sm">
                    @if (!empty($shippingAddress['name']))
                      <div>
                        <span class="text-muted">Name:</span>
                        <span class="ms-2">{{ $shippingAddress['name'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['address1']))
                      <div>
                        <span class="text-muted">Address1:</span>
                        <span class="ms-2">{{ $shippingAddress['address1'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['address2']))
                      <div>
                        <span class="text-muted">Address2:</span>
                        <span class="ms-2">{{ $shippingAddress['address2'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['city']))
                      <div>
                        <span class="text-muted">City:</span>
                        <span class="ms-2">{{ $shippingAddress['city'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['country']))
                      <div>
                        <span class="text-muted">Country:</span>
                        <span class="ms-2">{{ $shippingAddress['country'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['province']))
                      <div>
                        <span class="text-muted">Province:</span>
                        <span class="ms-2">{{ $shippingAddress['province'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['province_code']))
                      <div>
                        <span class="text-muted">Province code:</span>
                        <span class="ms-2">{{ $shippingAddress['province_code'] }}</span>
                      </div>
                    @endif
                    @if (!empty($shippingAddress['zip']))
                      <div>
                        <span class="text-muted">Zip:</span>
                        <span class="ms-2">{{ $shippingAddress['zip'] }}</span>
                      </div>
                    @endif
                  </div>
                </div>
              @endif

              <!-- Line Items -->
              @if (!empty($lineItems))
                <div class="p-4 border-0 border-bottom border-dashed">
                  <h6 class="fw-semibold mb-3">Order Items</h6>
                  <div class="d-flex flex-column gap-3">
                    @foreach ($lineItems as $itemIndex => $item)
                      <div class="d-flex gap-2" wire:key="order-{{ $order->id }}-item-{{ $itemIndex }}">
                        <div class="flex-grow-1">
                          <div class="text-dark">
                            <span>{{ $item['quantity'] ?? 1 }}×</span>
                            <span>{{ $item['name'] ?? ($item['title'] ?? 'Unknown Product') }}</span>
                          </div>
                          <div class="text-sm text-muted mt-1">
                            <div>
                              <span class="text-muted">Amount:</span>
                              <span class="ms-2 text-dark">${{ number_format($item['price'] ?? 0, 2) }}</span>
                            </div>
                            @if (!empty($item['sku']))
                              <div>
                                <span class="text-muted">SKU:</span>
                                <span class="ms-2 text-dark">{{ $item['sku'] }}</span>
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
                <div class="p-4 border-0 border-bottom border-dashed">
                  <h6 class="fw-semibold mb-3">Shipping Cost</h6>
                  @foreach ($shippingLines as $shipIndex => $shipping)
                    <div class="d-flex flex-column gap-1 text-sm"
                      wire:key="order-{{ $order->id }}-ship-{{ $shipIndex }}">
                      <div>
                        <span class="text-muted">Amount:</span>
                        <span class="ms-2 fw-medium">${{ number_format($shipping['price'] ?? 0, 2) }}
                          {{ $order->currency ?? 'USD' }}</span>
                      </div>
                      @if (!empty($shipping['title']))
                        <div>
                          <span class="text-muted">Code:</span>
                          <span class="ms-2">{{ $shipping['title'] }}</span>
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
                <div class="p-4">
                  <h6 class="fw-semibold mb-3">Shipment</h6>
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
                    <div class="d-flex flex-column gap-2 mb-2"
                      wire:key="order-{{ $order->id }}-fulfill-{{ $fulfillIndex }}">
                      <div>
                        <span
                          class="badge {{ $statusBadgeClass }}">{{ ucfirst(str_replace('_', ' ', $shipmentStatus)) }}</span>
                      </div>
                      <div class="text-sm">
                        @if (!empty($trackingInfo['number'] ?? $trackingInfo['tracking_number']))
                          <div>
                            <span class="text-muted">Tracking number:</span>
                            <span
                              class="ms-2">{{ $trackingInfo['number'] ?? $trackingInfo['tracking_number'] }}</span>
                          </div>
                        @endif
                        @if (!empty($trackingInfo['url'] ?? $trackingInfo['tracking_url']))
                          <div class="mt-1">
                            <span class="text-muted">Tracking URL:</span>
                            <a href="{{ $trackingInfo['url'] ?? $trackingInfo['tracking_url'] }}" target="_blank"
                              class="ms-2 text-primary text-break">
                              {{ Str::limit($trackingInfo['url'] ?? $trackingInfo['tracking_url'], 50) }}
                            </a>
                          </div>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="p-4">
                  <h6 class="fw-semibold mb-3">Shipment</h6>
                  <div class="text-sm text-muted fst-italic">
                    No shipment information available yet
                  </div>
                </div>
              @endif

            </div>
          </div>
        @endforeach
      </div>
    @endif
  </x-slot:body>

</x-offcanvas>
