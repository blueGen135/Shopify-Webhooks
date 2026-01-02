{{-- Customer Card --}}
<div class="row py-4">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-4">
          <div class="avatar avatar-lg me-3">
            <span class="avatar-initial rounded-3 bg-label-success">
              <img src="{{ asset('assets/img/customizer/vip.jpg') }}" class="w-7 h-7" alt="VIP" />
            </span>
          </div>
          <div class="flex-grow-1">
            <h5 class="mb-3">{{ $this->customer['name'] }}</h5>
            <a href="javascript:void(0);" class="text-dark fw-semi-bold" data-bs-toggle="offcanvas"
              data-bs-target="#customerDetailsOffcanvas">
              More details
              <i class="icon-base ti tabler-chevron-right text-success"></i>
            </a>
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
              <span><a href="mailto:{{ $this->customer['email'] }}">{{ $this->customer['email'] }}</a></span>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm flex-shrink-0 me-2">
              <span class="avatar avatar-sm flex-shrink-0 me-2 d-flex align-items-center justify-content-center">
                <img src="{{ asset('assets/img/customizer/phone.svg') }}" class="w-7 h-7" alt="Phone" />
              </span>
            </div>
            <div>
              <span>Phone:</span>
              <span>
                <a href="tel:{{ $customer['phone'] ?? '' }}">{{ $customer['phone'] ?? '' }}</a>
              </span>
            </div>
          </div>
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
            <span class="avatar-initial rounded-3 bg-label-success">
              <img src="{{ asset('assets/img/customizer/ord.jpg') }}" class="w-7 h-7" alt="Order" />
            </span>
          </div>
          <div class="flex-grow-1">
            <h5 class="mb-3">ORD- 23987</h5>
            <a href="#" class="text-dark fw-semi-bold">
              More details
              <i class="icon-base ti tabler-chevron-right text-success"></i>
            </a>
          </div>
        </div>
        <hr class="my-4" />
        <div class="d-flex flex-column gap-2">
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm flex-shrink-0 me-2">
              <img src="{{ asset('assets/img/customizer/calendar.svg') }}" class="w-7 h-7" alt="Date" />
            </div>
            <div>
              <span>Date:</span>
              <span>02-11-2025</span>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <div class="avatar avatar-sm flex-shrink-0 me-2">
              <img src="{{ asset('assets/img/customizer/mail.svg') }}" class="w-7 h-7" alt="Ordered" />
            </div>
            <div>
              <span>Ordered:</span>
              <span>25 days ago</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
