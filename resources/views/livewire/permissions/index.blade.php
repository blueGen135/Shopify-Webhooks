<div class="row">
  <div class="col-12">
    @include('components.flash')

    <x-table>
      <x-slot name="left">Permissions</x-slot>

      <x-slot name="right">
        <a wire:navigate href="{{ route('permissions.create') }}" class="btn btn-primary btn-icon-text">
          <x-zicon class="tabler-plus icon-sm me-0 me-sm-2" />
          Create Permission
        </a>
      </x-slot>

      <x-slot name="body">
        @livewire('permissions.table')
      </x-slot>
    </x-table>

    <x-modal.confirm id="deleteConfirmModal" title="Delete Permission"
      body="Are you sure you want to delete this permission? This action cannot be undone." confirmButtonText="Delete"
      confirmButtonClass="btn-danger" confirmButtonAction="wire:click.prevent='confirmDelete'"></x-modal.confirm>
  </div>
</div>
