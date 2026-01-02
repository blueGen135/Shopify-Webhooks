<div>
  <div class="row mb-5">
    <div class="col-lg-3 col-sm-6">
      <div class="card card-border-shadow-green h-70 cursor-pointer">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-primary">
                <img src="{{ asset('assets/img/customizer/active-ticket.jpg') }}" class="w-7 h-7">
              </span>
            </div>
            <div>
              <p class="mb-1">Active Tickets</p>
              <h3 class="mb-0 fw-bold">{{ $this->activeTicketsCount }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- GREEN CARD 2 --}}
    <div class="col-lg-3 col-sm-6">
      <div class="card card-border-shadow-green h-70 cursor-pointer">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-primary">
                <img src="{{ asset('assets/img/customizer/file-check.jpg') }}" class="w-7 h-7">
              </span>
            </div>
            <div>
              <p class="mb-1">Avg resolution time</p>
              <h3 class="mb-0 fw-bold">{{ $this->avgResolutionTime }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-sm-6">
      <div class="card card-border-shadow-green h-70 cursor-pointer">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-primary">
                <img src="{{ asset('assets/img/customizer/file-code.jpg') }}" class="w-7 h-7">
              </span>
            </div>
            <div>
              <p class="mb-1">Tickets in last 30 days</p>
              <h3 class="mb-0 fw-bold">{{ $this->ticketsLast30DaysCount }}</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
    {{-- PINK CARD #E97171 --}}
    <div class="col-lg-3 col-sm-6">
      <div class="card card-border-shadow-pink h-70 cursor-pointer">
        <div class="card-body">
          <div class="d-flex align-items-center mb-2">
            <div class="avatar me-4">
              <span class="avatar-initial rounded bg-label-danger">
                <img src="{{ asset('assets/img/customizer/file-alert.jpg') }}" class="w-7 h-7">
              </span>
            </div>
            <div>
              <p class="mb-1">Tickets with error</p>
              <h3 class="mb-0 fw-boldtext-danger">05</h3>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
