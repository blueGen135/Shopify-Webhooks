<?php

namespace App\Livewire\Roles;

use App\Traits\HasTableDeleteRow;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Roles & Permissions')]
class Index extends Component
{
    use HasTableDeleteRow;

    public function validateDelete(): ?string
    {
        $auth = auth()->user();
        if (!$auth || !$auth->can('roles.delete')) {
            return 'Unauthorized';
        }

        if ($this->selectedDelete === null) {
            return 'No role selected.';
        }

        $role = Role::find($this->selectedDelete);
        if (!$role) {
            return 'Role not found.';
        }

        if ($role->name === 'superadmin' || $role->name === 'agent') {
            return "You cannot delete the superadmin or agent roles.";
        }

        return null;
    }

    public function performDelete(): bool
    {
        $role = Role::find($this->selectedDelete);

        if (!$role) {
            return false;
        }

        return $role->delete();
    }

    protected function getDeleteSuccessMessage(): string
    {
        return 'Role deleted successfully.';
    }


    public function render()
    {
        return view('livewire.roles.index');
    }
}
