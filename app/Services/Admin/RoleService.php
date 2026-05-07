<?php
namespace App\Services\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Spatie\Permission\Models\Role;

class RoleService
{
    private string $guard = 'api';

    public function assignRole(array $data): User
    {
        $user = User::findOrFail($data['user_id']);

        $role = Role::where('name', $data['role'])
            ->where('guard_name', $this->guard)
            ->first();

        if (! $role) {
            throw new ModelNotFoundException('Role not found');
        }

        $user->assignRole($role);

        return $user->load('roles');
    }

    public function revokeRole(array $data): User
    {
        $user = User::findOrFail($data['user_id']);

        $role = Role::where('name', $data['role'])
            ->where('guard_name', $this->guard)
            ->first();

        if (! $role) {
            throw new ModelNotFoundException('Role not found');
        }

        $user->removeRole($role);

        return $user->load('roles');
    }

    public function updateRole(array $data): User
    {
        $user = User::findOrFail($data['user_id']);

        $role = Role::where('name', $data['role'])
            ->where('guard_name', $this->guard)
            ->first();

        if (! $role) {
            throw new ModelNotFoundException('Role not found');
        }

        $user->syncRoles([$role]);

        return $user->load('roles');
    }
}