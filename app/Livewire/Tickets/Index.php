<?php

namespace App\Livewire\Tickets;

use App\Models\Ticket;
use App\Traits\HasTableDeleteRow;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Tickets')]
class Index extends Component
{
  use HasTableDeleteRow;

  public function validateDelete(): ?string
  {
    $auth = auth()->user();
    if (!$auth || !$auth->can('tickets.delete')) {
      return 'Unauthorized.';
    }

    if ($this->selectedDelete === null) {
      return 'No ticket selected.';
    }

    $ticket = Ticket::find($this->selectedDelete);
    if (!$ticket) {
      return 'Ticket not found.';
    }

    return null;
  }

  protected function getDeleteSuccessMessage(): string
  {
    return 'Ticket deleted successfully.';
  }

  public function performDelete(): bool
  {
    $ticket = Ticket::find($this->selectedDelete);

    if (!$ticket) {
      return false;
    }

    return $ticket->delete();
  }

  public function getActiveTicketsCountProperty()
  {
    return Ticket::whereIn('status', ['open', 'pending'])->count();
  }

  public function getTicketsLast30DaysCountProperty()
  {
    return Ticket::where('created_datetime', '>=', now()->subDays(30))->count();
  }

  public function getAvgResolutionTimeProperty()
  {
    return Cache::remember('avg_resolution_time', 3600, function () {
      $closedTickets = Ticket::whereNotNull('created_datetime')
        ->whereNotNull('closed_datetime')
        ->get();

      if ($closedTickets->isEmpty()) {
        return 'N/A';
      }

      $totalSeconds = 0;
      $count = 0;

      foreach ($closedTickets as $ticket) {
        $created = $ticket->created_datetime;
        $closed = $ticket->closed_datetime;

        if ($created && $closed) {
          $totalSeconds += $created->diffInSeconds($closed, false);
          $count++;
        }
      }

      if ($count === 0) {
        return 'N/A';
      }

      $avgSeconds = $totalSeconds / $count;

      // Less than 1 hour - show in minutes
      if ($avgSeconds < 3600) {
        $avgMinutes = round($avgSeconds / 60);
        return $avgMinutes . ' min' . ($avgMinutes != 1 ? 's' : '');
      }

      // Less than 1 day - show in hours
      if ($avgSeconds < 86400) {
        $avgHours = round($avgSeconds / 3600, 1);
        return $avgHours . ' hour' . ($avgHours != 1 ? 's' : '');
      }

      // 1 day or more - show in days
      $avgDays = round($avgSeconds / 86400, 1);
      return $avgDays . ' day' . ($avgDays != 1 ? 's' : '');
    });
  }

  public function render()
  {
    return view('livewire.tickets.index');
  }
}
