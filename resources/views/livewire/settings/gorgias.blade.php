<div class="container-fluid flex-grow-1 container-p-y">

  <div class="row g-6">

    @include('livewire.settings.partials.navigation')

    <div class="col-12 col-lg-8 pt-6 pt-lg-0">
      <div class="tab-content p-0">
        <!-- Store Details Tab -->
        <div class="tab-pane fade show active" id="store_details" role="tabpanel">

          @include('components.flash')

          <form wire:submit.prevent="save">
            <div class="card mb-6">
              <div class="card-header">
                <h5 class="card-title m-0">Gorgias API Details</h5>
              </div>
              <div class="card-body">

                <div class="row mb-6 g-6">
                  <div class="col-12">
                    <label for="gorgiasDomain" class="form-label">Gorgias Domain</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                      wire:model="gorgiasDomain" placeholder="Gorgias Domain" id="gorgiasDomain">
                    @error('gorgiasDomain')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label for="gorgiasEmail" class="form-label">Gorgias Email</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                      wire:model="gorgiasEmail" placeholder="Gorgias Email" id="gorgiasEmail">
                    @error('gorgiasEmail')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="col-12 form-password-toggle">
                    <label for="gorgiasApiKey" class="form-label">Gorgias API Key</label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('name') is-invalid @enderror"
                        wire:model="gorgiasApiKey" placeholder="Gorgias API Key" id="gorgiasApiKey">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('gorgiasApiKey')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                </div>

              </div>
            </div>

            <div class="d-flex justify-content-end gap-4">
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>

          </form>

          <div class="card mt-6 mb-6">
            <div class="card-header">
              <h5 class="card-title m-0">Sync Gorgias Data</h5>
            </div>
            <div class="card-body">

              <div class="row mb-6 g-6">
                <div class="col-12">
                  <button type="button" class="btn btn-primary" wire:click="syncTags" wire:loading.attr="disabled"
                    wire:target="syncTags">
                    <span wire:loading.remove wire:target="syncTags">
                      Sync Tags
                    </span>
                    <span wire:loading wire:target="syncTags">
                      <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                      Syncing...
                    </span>
                  </button>

                  <button type="button" class="btn btn-primary ms-2" wire:click="syncCustomFields"
                    wire:loading.attr="disabled" wire:target="syncCustomFields">
                    <span wire:loading.remove wire:target="syncCustomFields">
                      Sync Custom Fields
                    </span>
                    <span wire:loading wire:target="syncCustomFields">
                      <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                      Syncing...
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>



        </div>
      </div>
    </div>

  </div>

</div>
