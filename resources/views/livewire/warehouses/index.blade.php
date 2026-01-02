<div>
  <div class="row">
    <div class="col-12">
      @include('components.flash')

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Warehouses</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#warehouseModal"
            wire:click="resetForm">
            <i class="icon-base ti tabler-plus me-1"></i> Add Warehouse
          </button>
        </div>

        <div class="card-body">
          <!-- Search -->
          <div class="row mb-3">
            <div class="col-md-4">
              <input type="text" class="form-control" placeholder="Search warehouses..." wire:model.live="search">
            </div>
          </div>

          <!-- Warehouses Table -->
          <div class="table-responsive">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Address</th>
                  <th>City</th>
                  <th>State</th>
                  <th>Postal Code</th>
                  <th>Coordinates</th>
                  <th>Priority</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($warehouses as $warehouse)
                  <tr>
                    <td><strong>{{ $warehouse->name }}</strong></td>
                    <td>{{ $warehouse->address }}</td>
                    <td>{{ $warehouse->city }}</td>
                    <td>{{ $warehouse->state }}</td>
                    <td>{{ $warehouse->postal_code }}</td>
                    <td>
                      @if ($warehouse->latitude && $warehouse->longitude)
                        <small class="text-muted">{{ $warehouse->latitude }}, {{ $warehouse->longitude }}</small>
                      @else
                        <small class="text-muted">N/A</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-label-primary">{{ $warehouse->priority }}</span>
                    </td>
                    <td>
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" @checked($warehouse->status)
                          wire:click="toggleStatus({{ $warehouse->id }})" wire:loading.attr="disabled">
                      </div>
                    </td>
                    <td>
                      <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                        wire:click="edit({{ $warehouse->id }})" data-bs-toggle="modal"
                        data-bs-target="#warehouseModal">
                        <i class="icon-base ti tabler-edit icon-md"></i>
                      </button>
                      <button class="btn btn-icon btn-text-secondary rounded-pill waves-effect"
                        wire:click="delete({{ $warehouse->id }})"
                        wire:confirm="Are you sure you want to delete this warehouse?">
                        <i class="icon-base ti tabler-trash icon-md"></i>
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center py-4">
                      <p class="text-muted mb-0">No warehouses found</p>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div class="mt-3">
            {{ $warehouses->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Warehouse Modal -->
  <div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true" wire:ignore.self>
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">{{ $isEditMode ? 'Edit Warehouse' : 'Add Warehouse' }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            wire:click="resetForm"></button>
        </div>
        <div class="modal-body">
          <form wire:submit.prevent="save">
            <div class="row g-3">
              <!-- Name -->
              <div class="col-md-12">
                <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
                  placeholder="789 Global Logistics Hub Innovation Park, San Alto">
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Address -->
              <div class="col-md-12">
                <label for="address" class="form-label">Street Address <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('address') is-invalid @enderror" wire:model="address"
                  placeholder="600 Pennsylvania Avenue NW">
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- City -->
              <div class="col-md-4">
                <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('city') is-invalid @enderror" wire:model="city"
                  placeholder="San Alto">
                @error('city')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- State -->
              <div class="col-md-4">
                <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('state') is-invalid @enderror" wire:model="state"
                  placeholder="CA" maxlength="2">
                @error('state')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Postal Code -->
              <div class="col-md-4">
                <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('postal_code') is-invalid @enderror"
                  wire:model="postal_code" placeholder="94301">
                @error('postal_code')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Country Code -->
              <div class="col-md-4">
                <label for="country_code" class="form-label">Country Code <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('country_code') is-invalid @enderror"
                  wire:model="country_code" placeholder="US" maxlength="2">
                @error('country_code')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>

              <!-- Latitude -->
              <div class="col-md-4">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="text" class="form-control @error('latitude') is-invalid @enderror"
                  wire:model="latitude" placeholder="37.4419">
                @error('latitude')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Optional: For distance calculations</small>
              </div>

              <!-- Longitude -->
              <div class="col-md-4">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="text" class="form-control @error('longitude') is-invalid @enderror"
                  wire:model="longitude" placeholder="-122.1430">
                @error('longitude')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Optional: For distance calculations</small>
              </div>

              <!-- Priority -->
              <div class="col-md-6">
                <label for="priority" class="form-label">Priority</label>
                <input type="number" class="form-control @error('priority') is-invalid @enderror"
                  wire:model="priority" min="0" placeholder="0">
                @error('priority')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">Higher priority warehouses appear first</small>
              </div>

              <!-- Status -->
              <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" wire:model="status" id="status">
                  <label class="form-check-label" for="status">
                    {{ $status ? 'Active' : 'Inactive' }}
                  </label>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"
            wire:click="resetForm">Cancel</button>
          <button type="button" class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
            <span wire:loading.remove wire:target="save">
              {{ $isEditMode ? 'Update Warehouse' : 'Create Warehouse' }}
            </span>
            <span wire:loading wire:target="save">
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Saving...
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>

  @script
    <script>
      $wire.on('open-modal', () => {
        $('#warehouseModal').modal('show');
      });

      $wire.on('close-modal', () => {
        $('#warehouseModal').modal('hide');
      });
    </script>
  @endscript

</div>
