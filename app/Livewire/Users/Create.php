<?php

namespace App\Livewire\Users;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\User;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Create User')]
class Create extends Component
{
    public string $name = '';
    public string $email = '';
    public bool $status = true;
    public string $password = '';
    public array $gorgiasUsers = [];
    public array $selectedRoles = [];
    public ?int $gorgias_user_id = null;
    public bool $showGorgiasAgents = false;
    public string $password_confirmation = '';

    public function mount()
    {
        $this->gorgiasUsers = User::fetchGorgiasUsers();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'selectedRoles' => ['required', 'array', 'min:1'],
            'selectedRoles.*' => ['integer', 'exists:roles,id'],
            'gorgias_user_id' => ['nullable', 'integer'],
            'status' => ['boolean'],
        ];
    }

    // Lifecycle hook
    public function updatedSelectedRoles()
    {
        $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
        $this->showGorgiasAgents = in_array('agent', $roles) || in_array('superadmin', $roles);
    }

    public function save()
    {
        if (!auth()->user()->can('users.create')) {
            abort(403);
        }

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

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => bcrypt($this->password),
            'gorgias_user_id' => $this->gorgias_user_id,
            'gorgias_email' => $gorgiasEmail,
            'gorgias_name' => $gorgiasName,
            'status' => $this->status,
        ]);

        if (!empty($this->selectedRoles)) {
            $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
            $user->syncRoles($roles);
        }

        return redirect()->route('users.index')->with('success', 'User created successfully.');
    }

    public function render()
    {
        return view('livewire.users.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
