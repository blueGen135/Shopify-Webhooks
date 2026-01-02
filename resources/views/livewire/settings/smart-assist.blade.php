<div class="container-fluid flex-grow-1 container-p-y">

  <div class="row g-6">

    @include('livewire.settings.partials.navigation')

    <div class="col-12 col-lg-8 pt-6 pt-lg-0">
      <div class="tab-content p-0">
        <!-- Smart Assist Settings Tab -->
        <div class="tab-pane fade show active" id="smart_assist" role="tabpanel">

          @include('components.flash')

          <form wire:submit.prevent="save">
            <div class="card mb-6">
              <div class="card-header">
                <h5 class="card-title m-0">Smart Assist Configuration</h5>
                <p class="text-muted mb-0 mt-1">Configure AI provider for automated response generation</p>
              </div>
              <div class="card-body">

                <div class="row mb-6 g-6">

                  <div class="col-12">
                    <label for="provider" class="form-label">AI Provider</label>
                    <select class="form-select @error('provider') is-invalid @enderror" wire:model="provider"
                      id="provider">
                      <option value="openai">OpenAI</option>
                    </select>
                    @error('provider')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="col-12 form-password-toggle">
                    <label for="apiKey" class="form-label">API Key</label>
                    <div class="input-group input-group-merge">
                      <input type="password" class="form-control @error('apiKey') is-invalid @enderror"
                        wire:model="apiKey" placeholder="sk-..." id="apiKey">
                      <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                    </div>
                    @error('apiKey')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                  </div>

                  <div class="col-12">
                    <label for="model" class="form-label">Model</label>
                    <select class="form-select @error('model') is-invalid @enderror" wire:model="model" id="model">
                      <optgroup label="GPT-5 (Latest)">
                        <option value="gpt-5">GPT-5</option>
                        <option value="gpt-5-turbo">GPT-5 Turbo</option>
                      </optgroup>
                      <optgroup label="GPT-4">
                        <option value="gpt-4o">GPT-4o</option>
                        <option value="gpt-4o-mini">GPT-4o Mini</option>
                        <option value="gpt-4-turbo">GPT-4 Turbo</option>
                      </optgroup>
                      <optgroup label="GPT-3.5">
                        <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                      </optgroup>
                    </select>
                    @error('model')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Select the OpenAI model for generating responses</small>
                  </div>

                  <div class="col-12">
                    <label for="systemPrompt" class="form-label">System Prompt</label>
                    <textarea class="form-control @error('systemPrompt') is-invalid @enderror" wire:model="systemPrompt"
                      id="systemPrompt" rows="10"
                      placeholder="Enter the system prompt that defines how the AI should respond..."></textarea>
                    @error('systemPrompt')
                      <p class="invalid-feedback">{{ $message }}</p>
                    @enderror
                    <small class="text-muted">Define how the AI should behave and respond to customer inquiries. This
                      prompt guides the tone, style, and approach of generated responses. Be specific about your
                      expectations.</small>
                  </div>

                </div>

              </div>
            </div>

            <div class="d-flex justify-content-end gap-4">
              <button type="button" class="btn btn-outline-secondary" wire:click="testConnection"
                wire:loading.attr="disabled" wire:target="testConnection">
                <span wire:loading.remove wire:target="testConnection">Test Connection</span>
                <span wire:loading wire:target="testConnection">
                  <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                  Testing...
                </span>
              </button>
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>

          </form>

        </div>
      </div>
    </div>

  </div>

</div>
