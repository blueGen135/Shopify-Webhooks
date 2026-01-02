<?php

namespace App\Livewire\Roles;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Roles & Permissions')]
class Edit extends Component
{

    public Role $role;

    public $permissions;

    public string $name = '';

    public array $groupedPermissions = [];

    public array $selectedPermissions = [];

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions()->pluck('id')->toArray();

        $this->groupedPermissions = Permission::grouped();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:50', 'unique:roles,name,' . $this->role->id],
            'selectedPermissions' => ['required', 'array', 'min:1'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function update()
    {
        if (!auth()->user()->can('roles.edit')) {
            abort(403);
        }

        if ($this->role->name === 'superadmin' && !auth()->user()->hasRole('superadmin')) {
            abort(403, 'Cannot edit superadmin.');
        }

        $this->validate();

        $this->role->update(['name' => $this->name]);

        $permissions = Permission::whereIn('id', $this->selectedPermissions)
            ->pluck('name')
            ->toArray();

        $this->role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }


    public function render()
    {
        return view('livewire.roles.edit');
    }
}
