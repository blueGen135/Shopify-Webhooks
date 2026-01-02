<?php

namespace App\Livewire\Permissions;

use App\Traits\HasTableDeleteRow;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Permission;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Permissions')]
class Table extends DataTableComponent
{
    protected $model = Permission::class;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->sortable(),
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Roles')
                ->label(fn($row) => $row->roles->pluck('name')->implode(', '))
                ->searchable(
                    fn($query, $searchTerm) =>
                    $query->orWhereHas(
                        'roles',
                        fn($q) =>
                        $q->where('name', 'like', '%' . $searchTerm . '%')
                    )
                ),
            Column::make('Actions')
                ->label(fn($row) => $this->getActionButtons($row))
                ->html(),
        ];
    }

    private function getActionButtons(Permission $row): string
    {
        $auth = auth()->user();
        $canEdit = $auth && $auth->hasRole('superadmin');
        $canDelete = $auth && $auth->hasRole('superadmin');

        $html = '<div class="d-flex gap-1">';

        if ($canEdit) {
            $editUrl = route('permissions.edit', $row->id);
            $html .= sprintf(
                '<a wire:navigate href="%s" class="btn btn-icon btn-text-secondary rounded-pill waves-effect"><i class="icon-base ti tabler-edit icon-md"></i></a>',
                e($editUrl)
            );
        }

        if ($canDelete) {
            $html .= sprintf(
                '<button class="btn btn-icon btn-text-secondary rounded-pill waves-effect" title="Delete" wire:click="$parent.setSelectedDelete(%d)"><i class="icon-base ti tabler-trash icon-md"></i></button>',
                $row->id
            );
        }

        $html .= '</div>';

        return $html;
    }
}
