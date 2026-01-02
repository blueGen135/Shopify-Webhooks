<form wire:submit.prevent="save">
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">

          <h6 class="card-title">Create User</h6>

          <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" id="name" class="form-control @error('name') is-invalid @enderror"
              wire:model.defer="name">
            @error('name')
              <p class="invalid-feedback">{{ $message }}</p>
            @enderror
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" class="form-control @error('email') is-invalid @enderror"
              wire:model.defer="email">
            @error('email')
              <p class="invalid-feedback">{{ $message }}</p>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" class="form-control @error('password') is-invalid @enderror"
              wire:model.defer="password">
            @error('password')
              <p class="invalid-feedback">{{ $message }}</p>
            @enderror
          </div>

          <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" id="password_confirmation" class="form-control"
              wire:model.defer="password_confirmation">
          </div>

          <h6 class="mt-3 mb-2">Assign Roles</h6>
          @foreach ($roles as $role)
            <div class="form-check" wire:key="role-{{ $role->id }}">
              <input class="form-check-input" type="checkbox" value="{{ $role->id }}" id="role-{{ $role->id }}"
                wire:model.live="selectedRoles">
              <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
            </div>
          @endforeach

          {{-- Validation Error for roles --}}
          @error('selectedRoles')
            <x-field-error :message="$message" />
          @enderror

          {{-- This handles each:* validation errors --}}
          @error('selectedRoles.*')
            <x-field-error :message="$message" />
          @enderror

          {{-- Gorgias User Selection (only show when agent role is selected) --}}
          @if (!empty($gorgiasUsers) && $showGorgiasAgents)
            <div class="mb-3 mt-4">
              <label for="gorgias_user_id" class="form-label">Gorgias Agent</label>
              <select id="gorgias_user_id" class="js-example-basic-single form-select" wire:model="gorgias_user_id"
                data-width="100%">
                <option value="">-- Select Gorgias Agent --</option>
                @foreach ($gorgiasUsers as $user)
                  <option value="{{ $user['id'] }}">{{ $user['name'] }} ({{ $user['email'] }})</option>
                @endforeach
              </select>
            </div>
          @endif

          <div class="mb-3 mt-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" class="form-select" wire:model.defer="status">
              <option value="1">Enabled</option>
              <option value="0">Disabled</option>
            </select>
            @error('status')
              <p class="invalid-feedback d-block">{{ $message }}</p>
            @enderror
          </div>

          <div class="mt-4">
            <a wire:navigate href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create</button>
          </div>

        </div>
      </div>
    </div>
  </div>
</form>
