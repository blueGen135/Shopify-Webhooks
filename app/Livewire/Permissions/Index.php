<?php

namespace App\Livewire\Permissions;

use App\Traits\HasTableDeleteRow;
use App\Models\Permission;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Permissions')]
class Index extends Component
{
    use HasTableDeleteRow;

    public function validateDelete(): ?string
    {
        if (!auth()->user()?->hasRole('superadmin')) {
            return 'Only superadmins can delete permissions.';
        }

        return null;
    }

    public function performDelete(): bool
    {
        $permission = Permission::findOrFail($this->selectedDelete);

        if (!$permission) {
            return false;
        }

        return $permission->delete();
    }

    public function getDeleteSuccessMessage(): string
    {
        return 'Permission deleted successfully.';
    }

    public function render()
    {
        return view('livewire.permissions.index');
    }
}
