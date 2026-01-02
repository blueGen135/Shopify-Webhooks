<form wire:submit.prevent="save">
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">

          <h6 class="card-title">Create Role</h6>

          <div class="mb-3">
            <label for="roleName" class="form-label">Role Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model="name"
              placeholder="Role Name" id="roleName">
            @error('name')
              <p class="invalid-feedback">{{ $message }}</p>
            @enderror
          </div>

          <div class="row">
            @foreach ($groupedPermissions as $group => $items)
              <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                <h5 class="mt-4 mb-2 text-capitalize">{{ str_replace('-', ' ', $group) }}</h5>
                <hr class="mt-1 mb-3">

                @foreach ($items as $permission)
                  <div class="form-check mb-2">
                    <input type="checkbox" class="form-check-input" value="{{ $permission['id'] }}"
                      id="perm-{{ $permission['id'] }}" wire:model="selectedPermissions">

                    <label class="form-check-label" for="perm-{{ $permission['id'] }}">
                      {{ $permission['name'] }}
                    </label>
                  </div>
                @endforeach
              </div>
            @endforeach
          </div>

          {{-- Validation Error for permissions --}}
          @error('selectedPermissions')
            <x-field-error :message="$message" />
          @enderror

          {{-- This handles each:* validation errors --}}
          @error('selectedPermissions.*')
            <x-field-error :message="$message" />
          @enderror

          <a wire:navigate href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary me-2">Submit</button>

        </div>
      </div>
    </div>
  </div>
</form>
