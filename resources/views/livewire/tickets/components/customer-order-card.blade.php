{{-- Customer Card --}}
<div class="row py-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-4">
          <div class="avatar avatar-lg me-3">
            <span class="rounded-3">
              <img
                src="{{ asset('assets/img/customizer/' . ($customerOrders && $customerOrders->count() > 1 ? 'customer-vip' : 'customer') . '.svg') }}"
                class="w-7 h-7" alt="VIP" />
            </span>
          </div>
          <div class="flex-grow-1">
            <h5 class="mb-3">{{ $shopifyCustomer ? $shopifyCustomer->name : $this->customer['name'] }}</h5>
            @if (!$shopifyCustomer)
              <span class="badge bg-warning-subtle">No data fetched</span>
            @else
              <a href="javascript:void(0);" class="text-dark fw-semi-bold" data-bs-toggle="offcanvas"
                data-bs-target="#customerDetailsOffcanvas">
                More details
                <i class="icon-base ti tabler-chevron-right text-success"></i>
              </a>
            @endif
          </div>
        </div>
        <hr class="my-4 hr-dotted" />
        <div class="d-flex flex-column gap-2">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm flex-shrink-0 me-2">
              <span class="avatar avatar-sm flex-shrink-0 me-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('assets/img/customizer/email.svg') }}" class="w-7 h-7" alt="Email" />
              </span>
            </div>
            <div>
              <span class="">Email:</span>
              <span><a
                  href="mailto:{{ $shopifyCustomer ? $shopifyCustomer->email : $this->customer['email'] }}">{{ $shopifyCustomer ? $shopifyCustomer->email : $this->customer['email'] }}</a></span>
            </div>
          </div>
          @if ($shopifyCustomer)
            <div class="d-flex align-items-center">
              <div class="avatar avatar-sm flex-shrink-0 me-2">
                <span class="avatar avatar-sm flex-shrink-0 me-2 d-flex align-items-center justify-content-center">
                  <img src="{{ asset('assets/img/customizer/phone.svg') }}" class="w-7 h-7" alt="Phone" />
                </span>
              </div>
              <div>
                <span>Phone:</span>
                <span>
                  <a href="tel:{{ $shopifyCustomer->phone }}">{{ $shopifyCustomer->phone }}</a>
                </span>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- Order Card --}}
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-4">
          <div class="avatar avatar-lg me-3">
            <span class="rounded-3 bg-label-success">
              <img src="{{ asset('assets/img/customizer/ord.jpg') }}" class="w-7 h-7" alt="Order" />
            </span>
          </div>
          @empty($customerOrders->isNotEmpty())
            <span class="badge bg-warning-subtle">No data fetched</span>
          @elseif (!$ticket->order)
            <div class="d-flex flex-column gap-2">
              <span class="badge bg-warning-subtle">No order fetched for this ticket.</span>
              <a href="javascript:void(0);" class="text-dark d-inline-flex fw-semibold align-items-center"
                data-bs-toggle="offcanvas" data-bs-target="#selectOrderOffcanvas">
                Select Order
                <i class="ti tabler-chevron-right ms-1 text-success"></i>
              </a>
            </div>
          @else
            <div class="flex-grow-1">
              <h5 class="mb-3">ORD - {{ $ticket->order->order_number ?? 'N/A' }}</h5>
              <a href="javascript:void(0);" class="text-dark fw-semi-bold" data-bs-toggle="offcanvas"
                data-bs-target="#orderDetailOffcanvas">
                More details
                <i class="icon-base ti tabler-chevron-right text-success"></i>
              </a>
            </div>
            @endif

          </div>
          <hr class="my-4" />
          @if ($customerOrders->isNotEmpty() && $ticket->order)
            <div class="d-flex flex-column gap-2">
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-2">
                  <img src="{{ asset('assets/img/customizer/calendar.svg') }}" class="w-7 h-7" alt="Date" />
                </div>
                <div>
                  <span>Date:</span>
                  <span>{{ $ticket->order->parseDateFormat($ticket->order->shopify_created_at) }}</span>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <div class="avatar avatar-sm flex-shrink-0 me-2">
                  <img src="{{ asset('assets/img/customizer/mail.svg') }}" class="w-7 h-7" alt="Ordered" />
                </div>
                <div>
                  <span>Ordered:</span>
                  <span>{{ $ticket->order->created_date_for_humans }}</span>
                </div>
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
