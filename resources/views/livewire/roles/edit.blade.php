<form wire:submit.prevent="update">
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">

          <h6 class="card-title">Edit Role</h6>

          <div class="mb-3">
            <label class="form-label">Role Name</label>
            <input type="text" class="form-control" wire:model="name">
            @error('name')
              <p class="text-danger small">{{ $message }}</p>
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

            @error('selectedPermissions')
              <x-field-error :message="$message" />
            @enderror

            @error('selectedPermissions.*')
              <x-field-error :message="$message" />
            @enderror
          </div>

          <a wire:navigate href="{{ route('roles.index') }}" class="btn btn-secondary mt-4">Cancel</a>
          <button type="submit" class="btn btn-primary mt-4">Update</button>

        </div>
      </div>
    </div>
  </div>
</form>
