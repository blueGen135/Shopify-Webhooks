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
          <h4 class="mb-1">Welcome to {{ config('variables.templateName') }}! 👋</h4>
          <p class="mb-6">Please sign-in to your account and start the adventure</p>

          <form wire:submit.prevent="login" id="formAuthentication" class="mb-4">
            <div class="mb-6 form-control-validation">
              <label for="email" class="form-label">Email address</label>
              <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                placeholder="Email" wire:model="email" name="email" autocomplete="email" autofocus>
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
            <div class="my-8">
              <div class="d-flex justify-content-between">
                <div class="form-check mb-0 ms-2">
                  <input wire:model="remember" id="remember" name="remember" type="checkbox"
                    class="form-check-input" />
                  <label class="form-check-label" for="remember"> Remember Me </label>
                </div>
                <div class="mb-3">
                  <a wire:navigate href="/forgot-password" class="font-semibold text-indigo-600 hover:text-indigo-500">
                    Forgot your password?
                  </a>
                </div>
              </div>
            </div>
            <div class="mb-6">
              <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
            </div>
          </form>
        </div>
      </div>
      <!-- /Login -->
    </div>
  </div>
</div>
