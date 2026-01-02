<div class="container-fluid flex-grow-1 container-p-y">

  <div class="row g-6">

    @include('livewire.settings.partials.navigation')

    <div class="col-12 col-lg-8 pt-6 pt-lg-0">
      <div class="tab-content p-0">
        <!-- Shopify Settings Tab -->
        <div class="tab-pane fade show active" id="shopify_settings" role="tabpanel">

          @include('components.flash')

          <form wire:submit.prevent="save">
            <div class="card mb-6">
              <div class="card-header">
                <h5 class="card-title m-0">Shopify API Configuration</h5>
                <p class="text-muted mb-0 mt-1">Configure Shopify store connection and API credentials</p>
              </div>
              <div class="card-body">

                <div class="row mb-6 g-6">
                  <div class="col-12">
                    <label for="shopifyDomain" class="form-label">Store Domain <span
                        class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('shopifyDomain') is-invalid @enderror"
                      wire:model="shopifyDomain" placeholder="your-store.myshopify.com" id="shopifyDomain">
                    @error('shopifyDomain')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Your Shopify store domain (e.g., your-store.myshopify.com)</small>
                  </div>

                  <div class="col-12 form-password-toggle">
                    <label for="shopifyAccessToken" class="form-label">Access Token <span
                        class="text-danger">*</span></label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('shopifyAccessToken') is-invalid @enderror"
                        wire:model="shopifyAccessToken" placeholder="shpat_..." id="shopifyAccessToken">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('shopifyAccessToken')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Admin API access token for making API calls</small>
                  </div>

                  <div class="col-md-6 form-password-toggle">
                    <label for="shopifyApiKey" class="form-label">API Key</label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('shopifyApiKey') is-invalid @enderror"
                        wire:model="shopifyApiKey" placeholder="Enter API Key" id="shopifyApiKey">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('shopifyApiKey')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">API Key from your Shopify app</small>
                  </div>

                  <div class="col-md-6 form-password-toggle">
                    <label for="shopifyApiSecret" class="form-label">API Secret</label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('shopifyApiSecret') is-invalid @enderror"
                        wire:model="shopifyApiSecret" placeholder="Enter API Secret" id="shopifyApiSecret">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('shopifyApiSecret')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">API Secret from your Shopify app</small>
                  </div>

                  <div class="col-12 form-password-toggle">
                    <label for="shopifyWebhookSecret" class="form-label">Webhook Secret</label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('shopifyWebhookSecret') is-invalid @enderror"
                        wire:model="shopifyWebhookSecret" placeholder="Enter Webhook Secret" id="shopifyWebhookSecret">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('shopifyWebhookSecret')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Webhook secret for verifying incoming webhook requests</small>
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
