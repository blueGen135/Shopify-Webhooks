<?php

namespace App\Livewire\Users;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\User;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Users Table')]
class Table extends DataTableComponent
{
    protected $model = User::class;

    protected $listeners = ['refreshTable' => '$refresh'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setAdditionalSelects(['users.status']);
    }

    public function builder(): \Illuminate\Database\Eloquent\Builder
    {
        return User::query()->with('roles');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')->sortable(),
            Column::make('Name', 'name')->sortable()->searchable(),
            Column::make('Email', 'email')->sortable()->searchable(),
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
            Column::make('Status', 'status')
                ->label(fn($row) => $row->status ? '<span class="badge bg-label-success">Active</span>' : '<span class="badge bg-label-secondary">Inactive</span>')
                ->sortable()
                ->html(),
            Column::make('Actions')
                ->label(fn($row) => $this->getActionButtons($row))
                ->html(),
        ];
    }

    private function getActionButtons($row): string
    {
        $auth = auth()->user();
        $canEdit = $auth && $auth->can('users.edit');
        $canDelete = $auth && $auth->can('users.delete') && $auth->id !== $row->id;

        $html = '<div class="d-flex align-items-center">';

        if ($canEdit) {
            $editUrl = route('users.edit', $row->id);
            $html .= sprintf(
                '<a href="%s" class="btn btn-icon btn-text-secondary rounded-pill waves-effect"><i class="icon-base ti tabler-edit icon-md"></i></a>',
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
