<?php

namespace App\Livewire\Roles;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Action;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Roles & Permissions')]
class Table extends DataTableComponent
{
    protected $model = Role::class;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Name", "name")
                ->sortable(),
            Action::make('Dashboard')
                ->setRoute('dashboard')
                ->wireNavigate()
                ->setLabelAttributes([
                    'class' => 'text-xl',
                    'default' => true,
                ]),
            Column::make('Actions')
                ->label(fn($row) => $this->getActionButtons($row))
                ->html(),
        ];
    }

    private function getActionButtons($row): string
    {
        $auth = auth()->user();
        $canEdit = $auth && $auth->can('roles.edit');
        $canDelete = $auth && $auth->can('roles.delete') && $auth->id !== $row->id;

        $html = '<div class="d-flex gap-1">';

        if ($canEdit) {
            $editUrl = route('roles.edit', $row->id);
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
