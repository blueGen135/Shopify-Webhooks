{{-- Creating return labels --}}
<a href="javascript:void(0);" class="text-dark d-inline-flex fw-semibold align-items-center" data-bs-toggle="offcanvas"
  data-bs-target="#partialrefundOffcanvas">Partial refund summary</a>
<x-offcanvas id="partialrefundOffcanvas"
  title="Partial refund summary"
  {{-- onClose="closePartialRefund" --}}
  wire:ignore.self>
  <x-slot:body>
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body p-0">
        <div class="table-responsive refund-details-table bg-white rounded">
          <table class="table border-none mb-0 align-middle">
            <thead class="p-5">
              <tr class="border-bottom p-5">
                <th class="grey-g1">TYRE DETAILS</th>
                <th class="grey-g1 text-end">REQ</th>
                <th class="grey-g1 text-end">STOCK</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <h6 class="primary-black mb-0">Aivin Race Spike Running Shoes - 7.5</h6>
                  <span class="grey-g2 text-muted small">SKU DUMMY-0</span>
                </td>
                <td class="text-end">
                  <h6 class="fw-normal mb-0 grey-g1">01</h6>
                </td>
                <td class="text-end">
                  <h6 class="fw-normal mb-0 grey-g1">$117.00</h6>
                </td>
              </tr>
              <tr>
                <td>
                  <h6 class="primary-black mb-0">Aivin Race Spike Running Shoes - 7.5</h6>
                  <span class="grey-g2 text-muted small">SKU DUMMY-0</span>
                </td>
                <td class="text-end">
                  <h6 class="fw-normal mb-0 grey-g1">01</h6>
                </td>
                <td class="text-end">
                  <h6 class="fw-normal mb-0 grey-g1">$117.00</h6>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    {{-- refuund chart --}}
    <div class="card shadow-sm mt-5 mb-4">
      <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <span class="fw-medium primary-black">Original Shipping cost:</span>
        <div class="d-flex align-items-center gap-2">
          <span class="fw-semibold grey-g1">$220.92</span>
        </div>
      </div>
      <div class="card-body p-4 d-flex justify-content-between align-items-center">
        <span class="fw-medium primary-black">Original Shipping cost:</span>
        <div class="d-flex align-items-center gap-2">
          <img src="http://127.0.0.1:8000/assets/img/customizer/edit.svg" class="w-7 h-7" alt="">
          <span class="fw-semibold grey-g1">$220.92</span>
        </div>
      </div>
    </div>
    <a href="#" class="text-dark fw-semibold d-inline-flex justify-content-end w-100 align-items-center">
      Check refund chart
      <i class="ti tabler-chevron-right ms-1 text-success"></i>
    </a>
    <div class="mt-5 send-partial-refund-block p-3 d-flex align-items-start border-1 rounded-1">
      <div class="me-3 mt-1">
        <img src="http://127.0.0.1:8000/assets/img/customizer/alert-circle.svg" class="w-7 h-7" alt="">
      </div>
      <div class="flex-grow-1">
        <h5 class="primary-black mb-1">You’re about to issue a refund of $115.00 for Order No. 4234JDJD</h5>
        <p class="grey-g1 fs-6 text mb-0">Once confirmed, this amount will be credited to the customer’s original
          payment
          method within 5–7 business days.This action cannot be undone.</p>
      </div>
    </div>
    <div
      class="mt-5 border-success alert send-partial-refund-block p-3 d-flex align-items-start border-1 rounded-1">
      <div class="me-3 mt-1">
        <img src="http://127.0.0.1:8000/assets/img/customizer/circle-check.svg" class="w-7 h-7" alt="">
      </div>
      <div class="flex-grow-1">
        <h5 class="primary-black mb-1">You’re about to issue a refund of $115.00 for Order No. 4234JDJD</h5>
        <p class="grey-g1 fs-6 text mb-0">Once confirmed, this amount will be credited to the customer’s original
          payment
          method within 5–7 business days.This action cannot be undone.</p>
      </div>
    </div>
    {{-- card --}}
    <div class="card px-5 py-0 shadow-sm mb-5">
      <div class="card-header border-0 px-0  pb-4 border-bottom border-dashed">
        <div class="d-flex justify-content-between align-items-center">
          <h6 class="primary-black fw-semibold m-0"><img
              src="http://127.0.0.1:8000/assets/img/customizer/circle-check.svg"
              class="w-7 h-7 me-3" alt=""> Replacement: <span class="text-success-dark"> 2 SKUs</span>
          </h6>
          <div class="badges">
            <span class="fw-semibold badge bg-success-subtle text-success-dark">Completed</span>
          </div>
        </div>
      </div>
      <div class="card-body px-0 py-5">
        <div class="replacement-items-wrapper d-flex flex-column gap-4">
          <div class="replacement-items">
            <h6 class="mb-0 grey-g1">Aivin Race Spike Running Shoes - 7.5</h6>
            <span class="grey-g2 text-muted small">SKU DUMMY-0</span>
          </div>
        </div>
      </div>
    </div>
    <div class="mt-5 grey-g1 small text mb-0 bg-success-subtle stock-availability-mail-block p-4 rounded-1">
      <p>Dear Sarah,</p>
      <p>Thank you for reporting the incorrect tires. I've checked our inventory for the correct tires you
        ordered.
      </p>
      <p>CEAT SECURADRIVE - 215/45 R17 (4 units): Fully Available</p>
      <p>MRF ZLX - 195/60 R15 (2 units): <br>Partially Available (1/2)</p>
      <p>APOLLO AMAZER - 205/55 R16 (4 units): Currently Out of Stock <br>(Expected: Dec 5, 2025)</p>
      <p>For items that are not fully available, please choose one of these options for each tire:</p>
      <p class="mb-0">Partial Refund: Keep the wrong tires and receive compensation</p>
      <p class="mb-0">Full Refund: Return the wrong tires for a full refund</p>
      <p>Wait for Restock: We'll ship correct tires once restocked, and you return the wrong ones</p>
      <p>Please reply with your preferred resolution for each tire.</p>
      <p class="mb-0">Best regards,</p>
      <p>Customer Support Team</p>
    </div>

  </x-slot:body>
</x-offcanvas>
{{-- Creating return labels --}}
