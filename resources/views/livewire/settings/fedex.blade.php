<div class="container-fluid flex-grow-1 container-p-y">

  <div class="row g-6">

    @include('livewire.settings.partials.navigation')

    <div class="col-12 col-lg-8 pt-6 pt-lg-0">
      <div class="tab-content p-0">
        <!-- FedEx Settings Tab -->
        <div class="tab-pane fade show active" id="fedex_settings" role="tabpanel">

          @include('components.flash')

          <form wire:submit.prevent="save">
            <div class="card mb-6">
              <div class="card-header">
                <h5 class="card-title m-0">FedEx API Configuration</h5>
                <p class="text-muted mb-0 mt-1">Configure FedEx API credentials for tracking, label generation, and
                  reports</p>
              </div>
              <div class="card-body">

                <div class="row mb-6 g-6">
                  <div class="col-12">
                    <label for="fedexEnvironment" class="form-label">Environment <span
                        class="text-danger">*</span></label>
                    <select class="form-select @error('fedexEnvironment') is-invalid @enderror"
                      wire:model="fedexEnvironment" id="fedexEnvironment">
                      <option value="sandbox">Sandbox (Testing)</option>
                      <option value="production">Production (Live)</option>
                    </select>
                    @error('fedexEnvironment')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Select sandbox for testing or production for live API calls</small>
                  </div>

                  <div class="col-md-6 form-password-toggle">
                    <label for="fedexApiKey" class="form-label">API Key <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('fedexApiKey') is-invalid @enderror"
                        wire:model="fedexApiKey" placeholder="Enter FedEx API Key" id="fedexApiKey">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('fedexApiKey')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Your FedEx Web Services API Key</small>
                  </div>

                  <div class="col-md-6 form-password-toggle">
                    <label for="fedexSecretKey" class="form-label">Secret Key <span class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('fedexSecretKey') is-invalid @enderror"
                        wire:model="fedexSecretKey" placeholder="Enter FedEx Secret Key" id="fedexSecretKey">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('fedexSecretKey')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Your FedEx Web Services Secret Key</small>
                  </div>

                  <div class="col-md-6">
                    <label for="fedexAccountNumber" class="form-label">Account Number <span
                        class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('fedexAccountNumber') is-invalid @enderror"
                      wire:model="fedexAccountNumber" placeholder="Enter FedEx Account Number" id="fedexAccountNumber">
                    @error('fedexAccountNumber')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Your 9-digit FedEx account number</small>
                  </div>

                  <div class="col-md-6">
                    <label for="fedexMeterNumber" class="form-label">Meter Number <span
                        class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('fedexMeterNumber') is-invalid @enderror"
                      wire:model="fedexMeterNumber" placeholder="Enter FedEx Meter Number" id="fedexMeterNumber">
                    @error('fedexMeterNumber')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Required for creating return shipping labels</small>
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
