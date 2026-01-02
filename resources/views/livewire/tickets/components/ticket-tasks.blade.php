<div class="card mt-4 border-0 shadow-0">
  <div class="card-header border-0 pb-0">
    <h5 class="mb-0">Your tasks</h5>
  </div>
  <div class="card-body pt-3">
    <div class="accordion" id="accordionTasks">
      {{-- Verification --}}
      @if ($this->verificationTask)
        <div class="accordion-item border-0">
          <h2 class="accordion-header" id="headingOne">
            <button
              class="accordion-button collapsed bg-transparent px-0 py-0 border-0"
              type="button"
              data-bs-toggle="collapse"
              data-bs-target="#accordionTask-1">
              <div class="task-row d-flex align-items-center w-100 py-3">
                <div class="me-3 flex-shrink-0">
                  <img src="{{ asset('assets/img/customizer/verification.svg') }}" class="w-7 h-7" alt="Verification" />
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">Step 1: Verification</h6>
                  <p class="small mb-0 text-body-secondary">
                    Review images sent by customer and verify the query
                  </p>
                </div>
                <span class="badge {{ $this->verificationTask?->getBadgeClass() ?? 'pending-badge' }} mx-5 fw-light">
                  {{ $this->verificationTask?->getStatusLabel() ?? 'Pending' }}
                </span>
              </div>
            </button>
          </h2>
          <div id="accordionTask-1" class="accordion-collapse collapse"
            data-bs-parent="#accordionTasks" wire:ignore.self>
            <div class="accordion-body p-5 bg-custom" x-data="{ showAllMedia: false }">
              @php
                $groupedImageAttachments = $this->getImageAttachments();
              @endphp

              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Customer uploaded media:</h5>
                @if (count($groupedImageAttachments) > 1)
                  <a href="javascript:void(0);" class="text-dark fw-semibold d-inline-flex align-items-center"
                    @click="showAllMedia = !showAllMedia">
                    <span x-text="showAllMedia ? 'Hide media history' : 'View media history'"></span>
                    <i class="icon-base ti text-success ms-1"
                      :class="showAllMedia ? 'tabler-chevron-up' : 'tabler-chevron-right'"></i>
                  </a>
                @endif
              </div>

              @if (!empty($groupedImageAttachments))
                <div>
                  @foreach ($groupedImageAttachments as $dateKey => $group)
                    <div
                      class="mb-4"
                      @if ($loop->index > 0) x-show="showAllMedia" x-collapse @endif>
                      <p class="mb-2 small fw-semibold text-body-secondary">
                        Uploaded on {{ $group['formatted_date'] }} • {{ count($group['images']) }}
                        {{ Str::plural('image', count($group['images'])) }}
                      </p>
                      <div class="d-flex flex-wrap gap-3 mb-2">
                        @foreach ($group['images'] as $index => $attachment)
                          <x-image-thumbnail
                            :url="$attachment['url']"
                            :name="$attachment['name']"
                            :index="$index"
                            :wire-key="'task-img-' . $dateKey . '-' . $index"
                            width="96px"
                            height="96px" />
                        @endforeach
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="alert alert-info mb-4">
                  No images uploaded by customer yet.
                </div>
              @endif

              <h6 class="mb-2">Order details:</h6>
              @if ($ticket->order && !empty($ticket->order->line_items))
                @php
                  $lineItems = $ticket->order->line_items;
                  $verificationTaskFromTicket = $ticket->tasks->where('type', 'verification')->first();
                  $verificationTaskId = $verificationTaskFromTicket?->id;
                @endphp
                <div class="table-responsive bg-white rounded">
                  <table class="table mb-0 table-orders align-middle">
                    <thead>
                      <tr>
                        <th class="ps-4">TYRE DETAILS</th>
                        <th class="text-center">QUANTITY</th>
                        <th class="w-auto pe-12">ACTION</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($lineItems as $itemIndex => $item)
                        @php
                          // Get saved product status from database
                          $savedStatus = $verificationTaskId
                              ? \App\Models\ProductTaskStatus::where('task_id', $verificationTaskId)
                                  ->where('details->line_item_index', $itemIndex)
                                  ->first()
                              : null;
                          $savedAction = $savedStatus?->action ?? '';
                        @endphp
                        <tr wire:key="task-product-{{ $itemIndex }}">
                          <td class="ps-4">
                            <div class="text-dark">
                              {{ $item['name'] ?? ($item['title'] ?? 'Unknown Product') }}
                            </div>
                            @if (!empty($item['sku']))
                              <div class="small text-body-secondary">SKU {{ $item['sku'] }}</div>
                            @endif
                          </td>
                          <td class="text-center">{{ $item['quantity'] ?? 1 }}</td>
                          <td class="text-end pe-4">
                            <select class="form-select text-body-secondary" title="Select"
                              wire:change="updateProductAction('verification', {{ $itemIndex }}, $event.target.value)">
                              <option value="">Select status</option>
                              <option value="verified" {{ $savedAction === 'verified' ? 'selected' : '' }}>Verified
                              </option>
                              <option value="not_verified" {{ $savedAction === 'not_verified' ? 'selected' : '' }}>Not
                                Verified</option>
                              <option value="ready_for_replacement"
                                {{ $savedAction === 'ready_for_replacement' ? 'selected' : '' }}>Ready for Replacement
                              </option>
                            </select>
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              @else
                <div class="alert alert-warning">
                  No order associated with this ticket or order has no line items.
                </div>
              @endif
              <div class="d-flex justify-content-end mt-3">
                <button type="button" class="btn btn-dark text-success"
                  wire:click="markVerificationCompleted()"
                  wire:loading.attr="disabled"
                  @if (!$this->verificationTask?->allProductsCompleted()) disabled @endif>
                  <span wire:loading.remove wire:target="markVerificationCompleted">Mark order as verified</span>
                  <span wire:loading wire:target="markVerificationCompleted">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Processing...
                  </span>
                </button>
              </div>
            </div>
          </div>
        </div>
      @endif

      {{-- Inventory Check & resolution --}}
      @if ($this->inventoryTask)
        <div class="accordion-item border-0">
          <h2 class="accordion-header" id="headingsix">
            <button class="accordion-button collapsed bg-transparent px-0 py-0 border-0" type="button"
              data-bs-toggle="collapse" data-bs-target="#accordionTask-6">
              <div class="task-row d-flex align-items-center w-100 py-3">
                <div class="me-3 flex-shrink-0">
                  <img src="{{ asset('assets/img/customizer/inventory.svg') }}" class="w-7 h-7" alt="Inventory" />
                </div>
                <div class="flex-grow-1">
                  <h6 class="mb-1">Step 2: Inventory check & resolution</h6>
                  <p class="small mb-0 text-body-secondary">Review stock status and help customer choose their
                    preferred resolution.</p>
                </div>
                <span class="badge {{ $this->inventoryTask->getBadgeClass() ?? 'pending-badge' }} mx-5 fw-light">
                  {{ $this->inventoryTask->getStatusLabel() ?? 'Pending' }}
                </span>
              </div>
            </button>
          </h2>
          <div id="accordionTask-6" class="accordion-collapse collapse show" data-bs-parent="#accordionTasks"
            wire:ignore.self>
            <div class="accordion-body p-5 bg-grey">
              <h6 class="mb-2">Stock details:</h6>
              @if ($ticket->order && !empty($ticket->order->line_items))
                <div class="card border-0 shadow-sm mb-4">
                  <div class="card-body p-4">
                    <div class="table-responsive bg-white rounded">
                      <table class="table mb-0 align-middle">
                        <thead>
                          <tr>
                            <th class="ps-4">TYRE DETAILS</th>
                            <th class="text-center">REQ</th>
                            <th class="text-center">STOCK</th>
                            <th class="text-center">RESTOCK</th>
                            <th class="text-end pe-4">ACTION</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($ticket->order->line_items as $itemIndex => $item)
                            @php
                              // Get saved product status from database
                              $savedStatus = $this->inventoryTask
                                  ->productStatuses()
                                  ->where('details->line_item_index', $itemIndex)
                                  ->first();

                              $savedAction = $savedStatus->action;
                              $requiredQty = $item['quantity'] ?? 1;

                              // Get processed inventory data from model
                              $inventoryInfo = $savedStatus->getProcessedInventoryData($requiredQty);

                              $availableQty = $inventoryInfo['available_qty'];
                              $restockDate = $inventoryInfo['restock_date'];
                              $warehousesWithStock = $inventoryInfo['warehouses_with_stock'];
                              $hasStock = $inventoryInfo['has_stock'];
                              $singleWarehouseCanFulfill = $inventoryInfo['single_warehouse_can_fulfill'];
                            @endphp
                            <tr wire:key="inventory-product-{{ $itemIndex }}" @class(['bg-body' => !$hasStock && !$restockDate])>
                              <td class="ps-4">
                                <div class="fw-semibold" @class([
                                    'text-body' => $hasStock || $restockDate,
                                    'text-body-secondary' => !$hasStock && !$restockDate,
                                ])>
                                  {{ $item['name'] ?? ($item['title'] ?? 'Unknown Product') }}
                                </div>
                                @if (!empty($item['sku']))
                                  <div class="small text-body-secondary">SKU {{ $item['sku'] }}</div>
                                @endif
                              </td>
                              <td class="text-center" @class(['text-body-secondary' => !$hasStock && !$restockDate])>
                                {{ str_pad($requiredQty, 2, '0', STR_PAD_LEFT) }}
                              </td>
                              <td class="text-center">
                                <span class="d-inline-flex align-items-center" @class(['text-body-secondary' => !$hasStock && !$restockDate])>
                                  <div class="me-1 flex-shrink-0"
                                    role="button"
                                    wire:click="showWarehouseDetails({{ $itemIndex }})"
                                    wire:loading.attr="disabled"
                                    data-bs-toggle="tooltip"
                                    data-bs-original-title="Click to view warehouse details"
                                    title="Click to view warehouse details"
                                    style="cursor: pointer;">
                                    <img
                                      wire:loading.remove
                                      wire:target="showWarehouseDetails({{ $itemIndex }})"
                                      src="{{ asset('assets/img/customizer/' . ($warehousesWithStock > 1 ? 'building-multiple-warehouse.svg' : 'building-warehouse.svg')) }}">
                                    <span
                                      wire:loading
                                      wire:target="showWarehouseDetails({{ $itemIndex }})"
                                      class="spinner-border spinner-border-sm" role="status"
                                      aria-hidden="true"></span>
                                  </div>
                                  @if ($hasStock || $restockDate)
                                    <span @class([
                                        'mt-1',
                                        'text-success' => $hasStock && $singleWarehouseCanFulfill,
                                        'text-warning' => $hasStock && !$singleWarehouseCanFulfill,
                                        'text-danger' => !$hasStock && $restockDate,
                                    ])>
                                      {{ str_pad($availableQty, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                  @else
                                    <span class="text-na mt-1">NA</span>
                                  @endif
                                </span>
                              </td>
                              <td class="text-center" @class(['text-body-secondary' => !$hasStock && !$restockDate])>
                                @if ($restockDate)
                                  {{ \Carbon\Carbon::parse($restockDate)->format('jS M, Y') }}
                                @else
                                  -
                                @endif
                              </td>
                              <td class="text-end pe-4" @class(['text-body-secondary' => !$hasStock && !$restockDate])>
                                <select
                                  class="form-select text-body-secondary"
                                  title="Select"
                                  wire:change="updateProductAction('inventory', {{ $itemIndex }}, $event.target.value)"
                                  @if (!$hasStock && !$restockDate) disabled @endif>
                                  <option value="">Select</option>
                                  @if ($hasStock)
                                    <option value="ready_to_replace"
                                      {{ $savedAction === 'ready_to_replace' ? 'selected' : '' }}>Ready to replace
                                    </option>
                                  @endif
                                  <option value="full_refund" {{ $savedAction === 'full_refund' ? 'selected' : '' }}>
                                    Full Refund</option>
                                  <option value="partial_refund"
                                    {{ $savedAction === 'partial_refund' ? 'selected' : '' }}>Partial refund</option>
                                  @if ($restockDate)
                                    <option value="wait_for_restock"
                                      {{ $savedAction === 'wait_for_restock' ? 'selected' : '' }}>Wait for restock
                                    </option>
                                  @endif
                                </select>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              @else
                <div class="alert alert-warning">
                  No order associated with this ticket or order has no line items.
                </div>
              @endif

              {{-- Inform Customer Section --}}
              <div class="mb-4">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="me-5">
                    <h5 class="mb-3">Inform customer</h5>
                    <p class="mb-0">
                      Communicate inventory status and resolution options to the customer via email.
                    </p>
                  </div>

                  <div class="d-flex gap-3 mt-3">
                    <button type="button" class="btn btn-outline-dark" wire:click="composeEmail">
                      Compose Email
                    </button>
                    <button type="button" class="btn btn-dark text-success"
                      data-bs-toggle="offcanvas"
                      data-bs-target="#resolutionOffcanvas"
                      @if (!$this->canStartResolution()) disabled @endif>
                      Start Resolution
                    </button>
                  </div>
                </div>

                @if ($this->hasWaitForRestockAction())
                  <div class="form-check mt-5">
                    <input class="form-check-input" type="checkbox" id="customerAgreed"
                      value="1"
                      wire:model.live="customerAgreed"
                      wire:click="toggleCustomerAgreed">
                    <label class="form-check-label" for="customerAgreed">
                      Customer agreed
                    </label>
                  </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      @endif

      {{-- Task 2 --}}
      {{-- <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingTwo">
          <button class="accordion-button collapsed bg-transparent px-0 py-0 border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#accordionTask-2">
            <div class="task-row d-flex align-items-center w-100 py-3">
              <div class="me-3 flex-shrink-0">
                <img src="{{ asset('assets/img/customizer/comm-cost.svg') }}" class="w-7 h-7" alt="Inventory" />
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">Step 2: Communicate shipping cost</h6>
                <p class="small mb-0 text-body-secondary">View shipping cost & inform customer</p>
              </div>
              <span class="badge bg-label-ongoing fw-light mx-5">Ongoing</span>
            </div>
          </button>
        </h2>
        <div id="accordionTask-2" class="accordion-collapse collapse" data-bs-parent="#accordionTasks">
          <div class="accordion-body p-5 bg-grey">
            <div class="card border-0 shadow-sm mb-4">
              <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                  <h5 class="mb-0">Location details</h5>
                  <a href="#" class="text-dark fw-semibold d-inline-flex align-items-center">
                    View other warehouses
                    <i class="icon-base ti text-success tabler-chevron-right ms-1"></i>
                  </a>
                </div>

                <p class="mb-4 text-body fs-6">
                  789 Global Logistics Hub Innovation Park, 600 Pennsylvania
                </p>

                <div class="row">
                  <div class="col-md-3">
                    <h3 class="mb-1">$94.12</h3>
                    <p class="mb-0">Return shipping cost</p>
                  </div>
                  <div class="col-md-3">
                    <h3 class="mb-1">28 miles</h3>
                    <p class="mb-0">From customer</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-4">
              <div class="d-flex justify-content-between align-items-start">
                <div class="me-5">
                  <h5 class="mb-3">Inform customer</h5>
                  <p class="mb-0">
                    Communicate inventory status and resolution options to the customer via email.
                  </p>
                </div>

                <div class="d-flex gap-3 mt-3">
                  <button type="button" class="btn btn-outline-dark">
                    Compose Email
                  </button>
                  <button type="button" class="btn btn-dark text-success disabled">
                    Start Resalution
                  </button>
                </div>
              </div>

              <div class="form-check mt-5">
                <input class="form-check-input" type="checkbox" id="customerAgreed">
                <label class="form-check-label" for="customerAgreed">
                  Customer agreed
                </label>
              </div>
            </div>
          </div>
        </div>
      </div> --}}

      {{-- Task 3 --}}
      <!-- <div class="accordion-item border-0">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed bg-transparent px-0 py-0 border-0" type="button" data-bs-toggle="collapse" data-bs-target="#accordionTask-3">
                        <div class="task-row d-flex align-items-center w-100 py-3">
                            <div class="me-3 flex-shrink-0">
                                <img src="{{ asset('assets/img/customizer/ticket.svg') }}" class="w-7 h-7" alt="Ticket" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Step 3: Close ticket</h6>
                                <p class="small mb-0 text-body-secondary">Confirm all resolutions are processed and close the ticket</p>
                            </div>
                            <span class="badge bg-label-success mx-5 fw-light">Completed</span>
                        </div>
                    </button>
                  </div>
                </div>
            </div> -->

      {{-- Task 4 --}}
      <!-- <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingfour">
          <button class="accordion-button collapsed bg-transparent px-0 py-0 border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#accordionTask-4">
            <div class="task-row d-flex align-items-center w-100 py-3">
              <div class="me-3 flex-shrink-0">
                <img src="{{ asset('assets/img/customizer/order-tracking.svg') }}" class="w-7 h-7"
                  alt="Order Tracking" />
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">Step 1: Order tracking</h6>
                <p class="small mb-0 text-body-secondary">Have a look at order tracking to check the status.</p>
              </div>
              <span class="badge bg-label-info fw-light mx-5">Ongoing</span>
            </div>
          </button>
        </h2>
        <div id="accordionTask-4" class="accordion-collapse collapse" data-bs-parent="#accordionTasks">
          <div class="accordion-body p-5 bg-grey">
            {{-- Order Timeline --}}
            <div class="order-timeline mb-4">
              {{-- Delivery --}}
              <div class="timeline-item">
                <div class="timeline-marker">
                  <div class="timeline-dot timeline-dot-inactive">
                  </div>
                  <div class="timeline-line timeline-line-inactive"></div>
                </div>
                <div class="timeline-content">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="mb-0 text-body">Delivery</h6>
                  </div>
                  <p class="small mb-0">Your package will be delivered by Sept 20</p>
                </div>
              </div>

              {{-- In transit --}}
              <div class="timeline-item ms-1">
                <div class="timeline-marker">
                  <div class="timeline-intrans-dot timeline-dot-active">
                  </div>
                  <div class="timeline-line timeline-line-active"></div>
                </div>
                <div class="timeline-content">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="mb-0 text-body">In transit</h6>
                    <span class="small d-flex align-items-center">
                      <i class="ti tabler-clock me-1"></i>
                      Sept 18, 2025 - 2:15 PM
                    </span>
                  </div>
                  <p class="small mb-0">Your package is on its way to you</p>
                </div>
              </div>

              {{-- Shipped --}}
              <div class="timeline-item">
                <div class="timeline-marker">
                  <div class="timeline-dot timeline-dot-completed">
                    <i class="ti tabler-check text-white icon-base"></i>
                  </div>
                  <div class="timeline-line timeline-line-active"></div>
                </div>
                <div class="timeline-content">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="mb-0 text-body">Shipped</h6>
                    <span class="small d-flex align-items-center">
                      <i class="ti tabler-clock me-1"></i>
                      Sept 18, 2025 - 2:15 PM
                    </span>
                  </div>
                  <p class="small mb-0">Your package has left our warehouse</p>
                </div>
              </div>

              {{-- Order confirmed --}}
              <div class="timeline-item">
                <div class="timeline-marker">
                  <div class="timeline-dot timeline-dot-completed">
                    <i class="ti tabler-check text-white icon-base"></i>
                  </div>
                </div>
                <div class="timeline-content">
                  <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 class="mb-0 text-body">Order confirmed</h6>
                    <span class="small d-flex align-items-center">
                      <i class="ti tabler-clock me-1"></i>
                      Sept 18, 2025 - 2:15 PM
                    </span>
                  </div>
                  <p class="small mb-0">Your order has been received and is being prepared</p>
                </div>
              </div>
            </div>

            {{-- Order Status Section --}}
            <div class="mt-5">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h6 class="mb-1">Order status</h6>
                  <p class="small mb-0">Check with the FedEx facility & the warehouse to update the status & inform
                    customer</p>
                </div>
                <button type="button" class="btn btn-outline-dark px-4">
                  Compose Email
                </button>
              </div>

              <div class="mt-3">
                <select class="form-select text-body-secondary" style="max-width: 200px;">
                  <option selected>Select Status</option>
                  <option value="delivered">Delivered</option>
                  <option value="in_transit">In Transit</option>
                  <option value="shipped">Shipped</option>
                  <option value="confirmed">Order Confirmed</option>
                  <option value="delayed">Delayed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div> -->

      {{-- Task 5 --}}
      <div class="accordion-item border-0">
        <h2 class="accordion-header" id="headingfive">
          <button class="accordion-button collapsed bg-transparent px-0 py-0 border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#accordionTask-5">
            <div class="task-row d-flex align-items-center w-100 py-3">
              <div class="me-3 flex-shrink-0">
                <img src="{{ asset('assets/img/customizer/order-tracking.svg') }}" class="w-7 h-7"
                  alt="Order Tracking" />
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-1">Step 1: Close ticket</h6>
                <p class="small mb-0 text-body-secondary">Confirm all resolutions are processed and close the ticket
                </p>
              </div>
              <span class="badge bg-label-info fw-light mx-5">Ongoing</span>
            </div>
          </button>
        </h2>
        <div id="accordionTask-5" class="accordion-collapse collapse" data-bs-parent="#accordionTasks">
          <div class="accordion-body p-5 bg-grey">
            {{-- close ticket Section --}}
            <div class="mt-5">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <select class="form-select text-body-secondary" style="max-width: 200px;">
                    <option value="replacement">Replacement</option>
                  </select>
                </div>
                <button type="button" class="btn btn-dark text-success">Mark order as closed</button>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  @if ($this->canStartResolution())
    @include('livewire.tickets.partials.offcanvas.resolution-steps')
    <livewire:tickets.components.replacement-journey
      :$ticket
      :task="$this->inventoryTask"
      :key="'ticket-replacement-journey-' . $ticket->id . '-' . $this->inventoryTask->updated_at->timestamp" />
  @endif

  {{-- Warehouse Details Modal --}}
  <div class="modal fade" id="warehouseDetailsModal" tabindex="-1" aria-labelledby="warehouseDetailsModalLabel"
    aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title" id="warehouseDetailsModalLabel">Warehouse Stock Details</h5>
            @if ($selectedProductForWarehouse)
              <p class="mb-0 text-muted small">{{ $selectedProductForWarehouse['product_name'] ?? 'Product' }} (SKU:
                {{ $selectedProductForWarehouse['sku'] ?? 'N/A' }})</p>
            @endif
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if ($selectedProductForWarehouse && isset($selectedProductForWarehouse['inventory_data']['raw_data']))
            @php
              $rawData = $selectedProductForWarehouse['inventory_data']['raw_data'];
            @endphp
            @foreach ($rawData['companies'] ?? [] as $company)
              <div class="mb-4">
                <h6 class="fw-semibold mb-3">{{ $company['company_code'] ?? 'Company' }}</h6>
                <div class="table-responsive">
                  <table class="table">
                    <thead class="">
                      <tr>
                        <th>Warehouse</th>
                        <th class="text-center">Available Stock</th>
                        <th class="text-center">Inbound Shipments</th>
                        <th class="text-center">Expected Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($company['warehouses'] ?? [] as $warehouse)
                        <tr>
                          <td>
                            <div class="fw-semibold">{{ $warehouse['warehouse_name'] ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $warehouse['warehouse_code'] ?? 'N/A' }}</small>
                          </td>
                          <td class="text-center">
                            <span @class([
                                'badge',
                                'bg-success-subtle text-success-dark' =>
                                    ($warehouse['available_qty'] ?? 0) > 0,
                                'bg-secondary-subtle text-secondary' =>
                                    ($warehouse['available_qty'] ?? 0) === 0,
                            ])>
                              {{ $warehouse['available_qty'] ?? 0 }} units
                            </span>
                          </td>
                          <td class="text-center">
                            @php
                              $totalInbound = collect($warehouse['inbound_shipments'] ?? [])->sum('quantity');
                            @endphp
                            @if ($totalInbound > 0)
                              <span class="badge bg-info-subtle text-info">{{ $totalInbound }} units</span>
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                          <td class="text-center">
                            @php
                              $earliestEta = collect($warehouse['inbound_shipments'] ?? [])->min('eta_date');
                            @endphp
                            @if ($earliestEta)
                              {{ \Carbon\Carbon::parse($earliestEta)->format('jS M Y') }}
                            @else
                              <span class="text-muted">-</span>
                            @endif
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            @endforeach
          @else
            <div class="alert alert-info mb-0">
              <i class="ti tabler-info-circle me-2"></i>
              No warehouse data available for this product.
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
