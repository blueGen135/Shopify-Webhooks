{{-- Create Order Step --}}
<div class="create-order-step">

  @if ($createOrderStep === 'create_order')
    <div class="card shadow-none">
      <div class="card-body p-0">

        <div class="mb-4 p-5 bg-f4">
          <p class="text-muted mb-1 small">Customer's address:</p>
          <p class="mb-0">
            456 Sierra Distribution Center, Discovery Gardens, Palo Alto, CA 94306, United States
          </p>
        </div>

        @if ($addReturnLabel)
          <h6 class="fw-semibold my-5">Where would you like to receive tires :</h6>
          @if (!empty($shippingRates))
            @php
              $recommended = $shippingRates[0];
            @endphp
            <div class="shadow-sm warehouse-details rounded-3 p-5">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-3">Warehouse details</h5>
                <span class="badge bg-warning-subtle px-3 py-2 fw-light">Recommended</span>
              </div>
              <span class="grey-g2 text-muted small">{{ $recommended['warehouse_name'] }}</span>
              <p class="grey-g2 text-muted small mt-2">{{ $recommended['warehouse_address'] }}</p>
              <table class="mt-5 table table-borderless mb-0 align-middle">
                <thead>
                  <tr class="border-none">
                    <th class="p-0 fw-bold primary-b1">${{ number_format($recommended['shipping_cost'], 2) }}</th>
                    <th class="p-0 fw-bold primary-b1">{{ $recommended['distance'] }}
                      {{ $recommended['distance_unit'] }}
                    </th>
                    <th></th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="p-0 grey-g2">Shipping Cost</td>
                    <td class="p-0 grey-g2">Distance</td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          @endif
        @else
          <div class="alert alert-info d-flex align-items-center gap-3" role="alert">
            <i class="ti tabler-info-circle text-info icon-base"></i>
            <div>
              Return labels will not be generated for this order. You can proceed to create the order directly.
            </div>
          </div>
        @endif


      </div>
    </div>

    {{-- only show warehouse if the return label checkbox if checked and shipping rates are available --}}
    @if ($addReturnLabel && !empty($shippingRates) && count($shippingRates) > 1)
      <div class="py-5 gap-5 d-flex flex-col flex-column justify-content-end" x-data="{ showWarehouses: false }">
        <a href="javascript:void(0);" class="text-dark fw-semibold d-inline-flex align-items-end justify-content-end"
          @click="showWarehouses = !showWarehouses">
          <span x-text="showWarehouses ? 'Hide other warehouses' : 'View other warehouses'"></span>
          <i class="ti ms-1 text-success" :class="showWarehouses ? 'tabler-chevron-up' : 'tabler-chevron-down'"></i>
        </a>

        <div class="card p-0 shadow-sm" x-show="showWarehouses" x-collapse>
          <table class="table table-borderless mb-0 align-middle">
            <thead>
              <tr class="border-bottom">
                <th class="">WAREHOUSE DETAILS</th>
                <th class="text-end">DISTANCE</th>
                <th class="text-end">COST</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($shippingRates as $rate)
                <tr>
                  <td class="">
                    <label class="d-flex gap-3 align-items-start mb-0">
                      <input class="form-check-input mt-1" type="radio" name="warehouse"
                        wire:model.live="selectedWarehouseId"
                        value="{{ $rate['warehouse_id'] }}"
                        @if ($rate['is_recommended']) checked @endif>
                      <div>
                        <h6 class="mb-0 primary-black">{{ $rate['warehouse_name'] }}</h6>
                        <p class="mb-0 text-muted small">{{ $rate['warehouse_address'] }}</p>
                      </div>
                    </label>
                  </td>
                  <td class="text-end">
                    {{ $rate['distance'] }} {{ $rate['distance_unit'] }}
                  </td>
                  <td class="text-end">
                    ${{ number_format($rate['shipping_cost'], 2) }}
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

      </div>
    @endif
  @elseif($createOrderStep === 'order_created')
    <div class="order-created-wrapper">

      <!-- Success Alert -->
      <div class="order-success pb-4 alert d-flex align-items-center gap-2 border-success">
        <img src="{{ asset('assets/img/customizer/circle-check.svg') }}" class="w-7 h-7" alt="">
        <h5 class="primary-black mb-0">Order successfully created!</h5>
      </div>

      <!-- Order Number -->
      <div class="card shadow-sm mt-5 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
          <span class="fw-medium primary-black">Order number:</span>
          <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold grey-g1">342252124</span>
            <img src="{{ asset('assets/img/customizer/copy.svg') }}" class="w-7 h-7" alt="">
          </div>
        </div>
      </div>

      @if ($addReturnLabel)
        <!-- Return Labels -->
        <h6 class="my-5 pt-4 primary-black">Return labels :</h6>

        <x-file-item
          :name="'Continental extreme contact'"
          :meta="'(245/35 R19).pdf'"
          :viewUrl="'#'"
          :downloadUrl="'#'" />

        <x-file-item
          :name="'Continental extreme contact'"
          :meta="'(245/35 R19).pdf'"
          :viewUrl="'#'"
          :downloadUrl="'#'" />
      @endif

      <div class="shadow-sm warehouse-details rounded-3 p-5 my-5">
        <div class="d-flex flex-column justify-content-between align-items-start">
          <h5 class="mb-0">FedEx facility nearest to the customer</h5>
          <p class="mb-3 text-secondary">FedEx facility nearest to the customer</p>
        </div>

        <table class="mt-5 table table-borderless mb-0 align-middle">
          <thead>
            <tr class="border-none">
              <th class="p-0 fw-bold primary-b1">400 miles
              </th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="p-0 grey-g2">From customer</td>
            </tr>
          </tbody>
        </table>

      </div>
    </div>
  @elseif($createOrderStep === 'send_email')
    <div class="send-email-wrapper">
      <div class="mt-5 mb-2 grey-g1 small text bg-success-subtle stock-availability-mail-block p-4 rounded-1">
        {!! nl2br(e($emailBody)) !!}
      </div>

      <!-- File Item -->
      <x-file-item
        :name="'Continental extreme contact'"
        :meta="'(245/35 R19).pdf'"
        :viewUrl="'#'"
        :downloadUrl="'#'" />

      <x-file-item
        :name="'Continental extreme contact'"
        :meta="'(245/35 R19).pdf'"
        :viewUrl="'#'"
        :downloadUrl="'#'" />
    </div>
  @elseif ($createOrderStep === 'email_sent')
    <div class="email-sent-wrapper">

      <!-- Success Alert -->
      <div class="order-success pb-4 alert d-flex align-items-center gap-2 border-success">
        <img src="{{ asset('assets/img/customizer/circle-check.svg') }}" class="w-7 h-7" alt="">
        <h5 class="primary-black mb-0">Email sent to the customer</h5>
      </div>

      <!-- Shipping Labels -->
      <h6 class="my-5 pt-4 primary-black">Shipping labels :</h6>

      <!-- File Item -->
      <x-file-item
        :name="'Continental extreme contact'"
        :meta="'(245/35 R19).pdf'"
        :viewUrl="'#'"
        :downloadUrl="'#'" />

      <x-file-item
        :name="'Continental extreme contact'"
        :meta="'(245/35 R19).pdf'"
        :viewUrl="'#'"
        :downloadUrl="'#'" />
    </div>
  @elseif($createOrderStep === 'save_and_proceed')
    <div class="save-and-proceed-wrapper">
      <!-- Success Alert -->
      <div class="order-success pb-4 alert d-flex align-items-center gap-2 border-success">
        <img src="{{ asset('assets/img/customizer/circle-check.svg') }}" class="w-7 h-7" alt="">
        <h5 class="primary-black mb-0">You’ve successfully resolved the replacement of the tires</h5>
      </div>
    </div>
  @endif
</div>
