<?php

namespace App\Livewire\Users;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Edit User')]
class Edit extends Component
{
    public int $userId;
    public int $status = 1;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public array $gorgiasUsers = [];
    public array $selectedRoles = [];
    public ?int $gorgias_user_id = null;
    public bool $showGorgiasAgents = false;
    public string $password_confirmation = '';

    public function mount(User $user)
    {
        // Allow editing yourself
        if (auth()->id() !== $user->id && !auth()->user()->can('users.edit')) {
            abort(403);
        }

        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->status = (int) $user->status;
        $this->selectedRoles = $user->roles->pluck('id')->toArray();
        $this->gorgias_user_id = $user->gorgias_user_id;
        $this->gorgiasUsers = User::fetchGorgiasUsers();
        $this->updatedSelectedRoles();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,' . $this->userId],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'selectedRoles' => ['required', 'array', 'min:1'],
            'selectedRoles.*' => ['integer', 'exists:roles,id'],
            'gorgias_user_id' => ['nullable', 'integer'],
            'status' => ['boolean'],
        ];
    }

    // When roles change in edit view, fetch Gorgias users if agent role selected
    public function updatedSelectedRoles()
    {
        $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
        $this->showGorgiasAgents = in_array('agent', $roles) || in_array('superadmin', $roles);
    }

    public function save()
    {
        if (auth()->id() !== $this->userId && !auth()->user()->can('users.edit')) {
            abort(403);
        }

        // Basic validation
        $this->validate();

        // Only require gorgias_user_id for agent role (optional for superadmin)
        $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
        if (in_array('agent', $roles) && empty($this->gorgias_user_id)) {
            $this->addError('gorgias_user_id', 'Please select a Gorgias user for agent role.');
            return;
        }

        // Find selected Gorgias user details
        $gorgiasEmail = null;
        $gorgiasName = null;
        if ($this->gorgias_user_id) {
            $selectedGorgiasUser = collect($this->gorgiasUsers)->firstWhere('id', $this->gorgias_user_id);
            if ($selectedGorgiasUser) {
                $gorgiasEmail = $selectedGorgiasUser['email'];
                $gorgiasName = $selectedGorgiasUser['name'];
            }
        }

        $user = User::findOrFail($this->userId);
        $user->name = $this->name;
        $user->email = $this->email;
        $user->gorgias_user_id = $this->gorgias_user_id;
        $user->gorgias_email = $gorgiasEmail;
        $user->gorgias_name = $gorgiasName;
        $user->status = $this->status;

        // If the user can't edit others, prevent modifying roles & gorgias fields
        $isEditingSelf = false;
        if (auth()->id() === $this->userId && !auth()->user()->can('users.edit')) {
            $this->selectedRoles = User::find($this->userId)->roles->pluck('id')->toArray();
            $this->gorgias_user_id = User::find($this->userId)->gorgias_user_id;
            $isEditingSelf = true;
        }

        if (!empty($this->password)) {
            $user->password = bcrypt($this->password);
        }

        $user->save();

        $roles = [];
        if (!empty($this->selectedRoles)) {
            $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
        }

        $user->syncRoles($roles);

        return redirect()->route($isEditingSelf ? 'dashboard' : 'users.index')->with('success', 'User updated successfully.');
    }

    public function render()
    {
        return view('livewire.users.edit', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
