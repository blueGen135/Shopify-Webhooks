<?php

namespace App\Livewire\Permissions;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.contentNavbarLayout')]
#[Title('Edit Permission')]
class Edit extends Component
{
    public int $permissionId;
    public string $name = '';
    public array $selectedRoles = [];

    public function mount(Permission $permission)
    {
        $this->permissionId = $permission->id;
        $this->name = $permission->name;
        $this->selectedRoles = $permission->roles()->pluck('id')->toArray();
    }

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100', 'unique:permissions,name,' . $this->permissionId],
            'selectedRoles' => ['array'],
            'selectedRoles.*' => ['integer', 'exists:roles,id'],
        ];
    }

    public function update()
    {
        if (!auth()->user()?->hasRole('superadmin')) {
            abort(403, 'Only superadmins can edit permissions.');
        }

        $this->validate();

        $permission = Permission::findOrFail($this->permissionId);
        $permission->update([
            'name' => $this->name,
        ]);

        $roles = [];
        if (!empty($this->selectedRoles)) {
            $roles = Role::whereIn('id', $this->selectedRoles)->pluck('name')->toArray();
        }

        $permission->syncRoles($roles);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    public function render()
    {
        return view('livewire.permissions.edit', [
            'roles' => Role::orderBy('name')->get(),
        ]);
    }
}
