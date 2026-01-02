{{-- Confirmation Step --}}
<div class="confirmation-step">
  <div class="card p-5 pb-3">
    <div class="card-body p-0">
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

      <!-- Return Labels -->
      <h6 class="my-5 pt-4 primary-black">Return labels :</h6>

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

      {{-- Return labels ends --}}
      <div class="return-label-wrapper" bis_skin_checked="1">
        <div class="return-label-field d-flex align-items-center p-3 bg-f4 rounded mb-3" bis_skin_checked="1">
          <div class="form-check form-switch m-0" bis_skin_checked="1">
            <input class="form-check-input" type="checkbox" id="returnLabel" checked="">
          </div>
          <input type="text" class="small form-control grey-g1 p-0 border-0 me-3"
            placeholder="Add return labels">
          <a href="#"
            class="text-dark fw-semibold d-inline-flex justify-content-end w-100 align-items-center">
            Discount chart
            <i class="ti tabler-chevron-right ms-1 text-success"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
