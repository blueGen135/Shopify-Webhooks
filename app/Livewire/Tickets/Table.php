<?php

namespace App\Livewire\Tickets;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Ticket;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Tickets Table')]
class Table extends DataTableComponent
{
  protected $model = Ticket::class;

  protected $listeners = ['refreshTable' => '$refresh'];

  public function configure(): void
  {
    $this->setPrimaryKey('id')
      ->setAdditionalSelects(['tickets.id', 'tickets.status', 'tickets.priority', 'tickets.gorgias_ticket_id', 'tickets.is_unread'])
      ->setTableRowUrl(fn($row) => route('tickets.details', $row->gorgias_ticket_id));
  }

  public function builder(): \Illuminate\Database\Eloquent\Builder
  {
    return Ticket::query()->with(['user']);
  }

  public function columns(): array
  {
    return [
      Column::make('Ticket ID', 'gorgias_ticket_id')
        ->sortable()
        ->searchable(),

      Column::make('Subject', 'subject')
        ->sortable()
        ->searchable(),

      Column::make('Requester', 'requester_name')
        ->sortable()
        ->searchable()
        ->label(fn($row) => $row->requester_full_name),

      Column::make('Email', 'requester_email')
        ->sortable()
        ->searchable(),

      Column::make('Assigned To')
        ->label(fn($row) => $row->localAssignedTo ? $row->localAssignedTo->name : '<span class="text-muted">Unassigned</span>')
        ->html()
        ->searchable(
          fn($query, $searchTerm) =>
          $query->orWhereHas(
            'localAssignedTo',
            fn($q) =>
            $q->where('name', 'like', '%' . $searchTerm . '%')
          )
        ),

      Column::make('Priority', 'priority')
        ->label(fn($row) => $row->priority ? sprintf(
          '<span class="badge %s">%s</span>',
          $row->priority_badge,
          ucfirst($row->priority)
        ) : '<span class="text-muted">-</span>')
        ->sortable()
        ->html(),

      Column::make('Status', 'status')
        ->label(fn($row) => sprintf(
          '<span class="badge %s">%s</span>',
          $row->status_badge,
          ucfirst(str_replace('_', ' ', $row->status ?? 'unknown'))
        ))
        ->sortable()
        ->html(),

      Column::make('Contact Reason')
        ->label(fn($row) => $row->contact_reason ? '<span class="badge bg-label-info">' . e($row->contact_reason) . '</span>' : '<span class="text-muted">-</span>')
        ->html(),

      Column::make('Created', 'created_datetime')
        ->sortable()
        ->label(fn($row) => $row->created_datetime ? $row->created_datetime->format('M d, Y H:i') : '-'),
    ];
  }
}
