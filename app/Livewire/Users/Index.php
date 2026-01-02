<?php

namespace App\Livewire\Users;

use App\Traits\HasTableDeleteRow;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Users')]
class Index extends Component
{
    use HasTableDeleteRow;

    public function validateDelete(): ?string
    {
        $auth = auth()->user();
        if (!$auth || !$auth->can('users.delete')) {
            return 'Unauthorized.';
        }

        if ($this->selectedDelete === null) {
            return 'No user selected.';
        }

        if ($auth->id === $this->selectedDelete) {
            return 'You cannot delete your own account.';
        }

        $user = \App\Models\User::find($this->selectedDelete);
        if (!$user) {
            return 'User not found.';
        }

        if ($user->hasRole('superadmin') && !$auth->hasRole('superadmin')) {
            return 'Only superadmins can delete other superadmins.';
        }

        return null;
    }

    protected function getDeleteSuccessMessage(): string
    {
        return 'User deleted successfully.';
    }

    public function performDelete(): bool
    {
        $user = \App\Models\User::find($this->selectedDelete);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function render()
    {
        return view('livewire.users.index');
    }
}
