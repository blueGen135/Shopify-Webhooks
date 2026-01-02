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
          <h4 class="mb-1">Forgot your password?</h4>

          @if ($sent)
            <div class="mb-6">
              <p class="alert alert-success">
                Password reset link sent! Please check your email.
              </p>
            </div>
          @else
            <p class="mb-3">Enter your email address and we'll send you a link to reset your password.
            </p>
            <form wire:submit.prevent="sendResetLink" id="formAuthentication" class="mb-4">
              <div class="mb-6 form-control-validation">
                <label for="email" class="form-label">Email address</label>
                <input wire:model="email" id="email" name="email" type="email" autocomplete="email"
                  class="form-control @error('email') is-invalid @enderror" placeholder="Email address" />
                @error('email')
                  <p class="invalid-feedback">{{ $message }}</p>
                @enderror
              </div>

              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">Send Reset Link</button>
              </div>
            </form>
          @endif
          <div class="">
            <a wire:navigate href="/" class="btn btn-secondary d-grid w-100">Back to login</a>
          </div>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
</div>
