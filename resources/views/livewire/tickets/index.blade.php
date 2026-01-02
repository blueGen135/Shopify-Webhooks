<div class="row">
  <div class="col-12">

    @include('components.flash')

    @include('livewire.tickets.partials.header')

    <x-table>
      <x-slot name="left">
        <h4 class="card-title mb-0">Tickets</h4>
      </x-slot>

      <x-slot name="right"></x-slot>

      <x-slot name="body">
        @livewire('tickets.table')
      </x-slot>

    </x-table>

    @can('tickets.delete')
      <x-modal.confirm id="deleteConfirmModal" title="Delete Ticket"
        body="Are you sure you want to delete this ticket? This action cannot be undone." confirmButtonText="Delete"
        confirmButtonClass="btn-danger" confirmButtonAction="wire:click.prevent='confirmDelete'"
        onConfirm="confirmDelete"></x-modal.confirm>
    @endcan
  </div>
</div>
