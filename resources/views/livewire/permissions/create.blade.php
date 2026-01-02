<form wire:submit.prevent="save">
  <div class="row">
    <div class="col-md-12 grid-margin stretch-card">
      <div class="card">
        <div class="card-body">

          <h6 class="card-title">Create Permission</h6>

          <div class="mb-3">
            <label for="permissionName" class="form-label">Permission Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name"
              placeholder="Permission Name" id="permissionName">
            @error('name')
              <p class="invalid-feedback">{{ $message }}</p>
            @enderror
          </div>

          <h6 class="mt-4 mb-3">Assign to Roles</h6>
          <div class="row">
            @foreach ($roles as $role)
              <div class="col-6 col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                    id="role-{{ $role->id }}" wire:model="selectedRoles">
                  <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Validation Error for roles --}}
          @error('selectedRoles')
            <x-field-error :message="$message" />
          @enderror

          {{-- This handles each:* validation errors --}}
          @error('selectedRoles.*')
            <x-field-error :message="$message" />
          @enderror

          <a wire:navigate href="{{ route('permissions.index') }}" class="btn btn-secondary mt-4">Cancel</a>
          <button type="submit" class="btn btn-primary me-2 mt-4">Create</button>

        </div>
      </div>
    </div>
  </div>
</form>
