<div class="row">
  <div class="col-12">
    @include('components.flash')

    <x-table>
      <x-slot name="left">
        <h4 class="card-title mb-0">Users</h4>
      </x-slot>

      <x-slot name="right">
        <a wire:navigate href="{{ route('users.create') }}" class="btn btn-primary btn-icon-text">
          <x-zicon class="tabler-plus icon-sm me-0 me-sm-2" />
          Create User
        </a>
      </x-slot>

      <x-slot name="body">
        @livewire('users.table')
      </x-slot>

    </x-table>


    <x-modal.confirm id="deleteConfirmModal" title="Delete User"
      body="Are you sure you want to delete this user? This action cannot be undone." confirmButtonText="Delete"
      confirmButtonClass="btn-danger" confirmButtonAction="wire:click.prevent='confirmDelete'"
      onConfirm="confirmDelete"></x-modal.confirm>
  </div>
</div>
