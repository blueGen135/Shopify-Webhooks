<?php

namespace App\Livewire\Permissions;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Create Permission')]
class Create extends Component
{
    public string $name = '';
    public array $selectedRoles = [];

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:permissions,name'],
            'selectedRoles' => ['array'],
            'selectedRoles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function save()
    {
        if (!auth()->user()?->hasRole('superadmin')) {
            abort(403, 'Only superadmins can create permissions.');
        }

        $this->validate();

        $permission = Permission::create([
            'name' => $this->name,
        ]);

        if (!empty($this->selectedRoles)) {
            $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
            $permission->syncRoles($roles);
        }

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    public function render()
    {
        return view('livewire.permissions.create', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
