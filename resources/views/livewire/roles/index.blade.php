<div>
  @include('components.flash')

  <x-table>
    <x-slot name="left">Permissions</x-slot>

    <x-slot name="right">
      <a wire:navigate href="{{ route('roles.create') }}" class="btn btn-primary btn-icon-text">
        <x-zicon class="tabler-plus icon-sm me-0 me-sm-2" />
        Create Role
      </a>
    </x-slot>

    <x-slot name="body">
      @livewire('roles.table')
    </x-slot>
  </x-table>

  <x-modal.confirm id="deleteConfirmModal" title="Delete Role"
    body="Are you sure you want to delete this role? This action cannot be undone." confirmButtonText="Delete"
    confirmButtonClass="btn-danger" confirmButtonAction="wire:click.prevent='confirmDelete'"></x-modal.confirm>
</div>
