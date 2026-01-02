<?php

namespace App\Livewire\Roles;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Roles & Permissions')]
class Create extends Component
{
    public string $name = '';

    public array $groupedPermissions = [];

    public array $selectedPermissions = [];

    public function mount()
    {
        $this->groupedPermissions = Permission::grouped();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:50', 'unique:roles,name'],
            'selectedPermissions' => ['required', 'array', 'min:1'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function save()
    {
        if (!auth()->user()->can('roles.create'))
            abort(403);

        $this->validate();

        $role = Role::create(['name' => $this->name]);

        $permissions = Permission::whereIn('id', $this->selectedPermissions)
            ->pluck('name')
            ->toArray();

        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
    }

    public function render()
    {
        return view(
            'livewire.roles.create',
            ['permissions' => Permission::orderBy('name')->get()]
        );
    }
}
