{{-- Summary Step --}}
<div class="replacement-summary">
  @php
    $totalAmount = 0;
  @endphp

  <div class="card p-0 shadow-sm">

    <table class="table table-borderless mb-0 align-middle">
      <thead>
        <tr class="border-bottom">
          <th class="">Order Details</th>
          <th class="text-end">QTY</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($actionProducts as $status)
          <tr>
            <td>
              <h6 class="mb-0 grey-g1">{{ $status->details['product_name'] ?? 'Unknown Product' }}</h6>
              <span class="grey-g2 text-muted small">SKU {{ $status->details['sku'] ?? 'N/A' }}</span>
            </td>
            <td class="text-end">
              <span class="text-secondary">X</span>
              {{ str_pad($status->details['quantity'] ?? 1, 2, '0', STR_PAD_LEFT) }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

  </div>

  @if ($ticket->order && isset($ticket->order->shipping_lines))
    <div class="shadow-sm rounded-2 p-4 mt-4">
      <div class="d-flex flex-column gap-5">
        @php
          $shippingCost = 0;
          if (!empty($ticket->order->shipping_lines)) {
              foreach ($ticket->order->shipping_lines as $shipping) {
                  $shippingCost += floatval($shipping['price'] ?? 0);
              }
          }
        @endphp
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="mb-0 grey-g1">Original Shipping cost:</h6>
          <h6 class="mb-0 grey-g1 fw-semibold">${{ number_format($shippingCost, 2) }}</h6>
        </div>
      </div>
    </div>
  @endif
</div>
