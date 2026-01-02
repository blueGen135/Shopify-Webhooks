<div class="container-fluid flex-grow-1 container-p-y">

  <div class="row g-6">

    @include('livewire.settings.partials.navigation')

    <div class="col-12 col-lg-8 pt-6 pt-lg-0">
      <div class="tab-content p-0">
        <!-- Odoo Settings Tab -->
        <div class="tab-pane fade show active" id="odoo_settings" role="tabpanel">

          @include('components.flash')

          <form wire:submit.prevent="save">
            <div class="card mb-6">
              <div class="card-header">
                <h5 class="card-title m-0">Odoo API Configuration</h5>
                <p class="text-muted mb-0 mt-1">Configure Odoo connection for inbound status and inventory management
                </p>
              </div>
              <div class="card-body">

                <div class="row mb-6 g-6">
                  <div class="col-12">
                    <label for="odooEndpoint" class="form-label">Odoo Endpoint (URL) <span
                        class="text-danger">*</span></label>
                    <input type="url" class="form-control @error('odooEndpoint') is-invalid @enderror"
                      wire:model="odooEndpoint" placeholder="https://your-instance.odoo.com/api" id="odooEndpoint">
                    @error('odooEndpoint')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Your Odoo API endpoint URL</small>
                  </div>

                  <div class="col-12">
                    <label for="customerNumber" class="form-label">Customer Number <span
                        class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('customerNumber') is-invalid @enderror"
                      wire:model="customerNumber" placeholder="Enter customer number" id="customerNumber">
                    @error('customerNumber')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="col-12 form-password-toggle">
                    <label for="odooPassword" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('odooPassword') is-invalid @enderror"
                        wire:model="odooPassword" placeholder="Enter password" id="odooPassword">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('odooPassword')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label for="odooCompanies" class="form-label">Companies (comma separated IDs)</label>
                    <input type="text" class="form-control @error('odooCompanies') is-invalid @enderror"
                      wire:model="odooCompanies" placeholder="e.g. 1,2,3" id="odooCompanies">
                    @error('odooCompanies')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Enter company IDs separated by commas</small>
                  </div>
                </div>

              </div>
            </div>

            <div class="d-flex justify-content-end gap-4">
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>

          </form>

        </div>
      </div>
    </div>

  </div>

</div>
