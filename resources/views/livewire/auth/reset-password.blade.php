<div class="container-xxl">
  @section('page-style')
    @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
  @endsection
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <!-- Login -->
      <div class="card">
        <div class="card-body">
          <!-- Logo -->
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ url('/') }}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros')</span>
              <span class="app-brand-text demo text-heading fw-bold">{{ config('variables.templateName') }}</span>
            </a>
          </div>
          <!-- /Logo -->
          <h4 class="mb-1">Reset your password</h4>

          @if (!$tokenValid)
            <div class="alert alert-danger mb-6" role="alert">
              <p class="mb-0">{{ $tokenError }}</p>
            </div>

            <p class="mb-6">
              <a wire:navigate href="/forgot-password" class="btn btn-primary d-grid w-100">
                Request a new password reset link
              </a>
            </p>
            <p>
              <a href="/" class="btn btn-secondary d-grid w-100">
                Back to login
              </a>
            </p>
          @else
            <form wire:submit.prevent="resetPassword" id="formAuthentication" class="mb-4">
              <div class="mb-6 form-control-validation">
                <label for="email" class="form-label">Email address</label>
                <input wire:model="email" id="email" name="email" type="email" autocomplete="email"
                  class="form-control @error('email') is-invalid @enderror" disabled />
                @error('email')
                  <p class="invalid-feedback">{{ $message }}</p>
                @enderror
              </div>

              <div class="mb-6 form-password-toggle form-control-validation">
                <label for="password" class="form-label">Password</label>
                <div class="input-group input-group-merge">
                  <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                    autocomplete="current-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    wire:model="password">
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
                @error('password')
                  <p class="invalid-feedback">{{ $message }}</p>
                @enderror
              </div>

              <div class="mb-6 form-password-toggle form-control-validation">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="input-group input-group-merge">
                  <input wire:model="password_confirmation" id="password_confirmation" name="password_confirmation"
                    type="password" autocomplete="new-password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    class="form-control @error('password_confirmation') is-invalid @enderror" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
                </div>
                @error('password_confirmation')
                  <p class="invalid-feedback">{{ $message }}</p>
                @enderror
              </div>

              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">Reset Password</button>
              </div>
            </form>
          @endif
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
</div>
