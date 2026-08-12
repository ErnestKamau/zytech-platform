<?php

namespace Database\Seeders;

use App\Core\Enums\RoleType;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $permissions = [
        'users.view',
        'users.create',
        'users.update',
        'users.delete',
        'users.lock',
        'users.assign-role',
        'users.invite',
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',
        'settings.view',
        'settings.manage',
        'navigation.view',
        'navigation.manage',
        'feature-flags.view',
        'feature-flags.manage',
        'company.view',
        'company.update',
        'media.view',
        'media.manage',
        'services.view',
        'services.manage',
        'projects.view',
        'projects.manage',
    ];

    public function run(): void
    {
        foreach ($this->permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }

        foreach (RoleType::cases() as $roleType) {
            Role::findOrCreate($roleType->value, 'web');
        }

        Role::findByName(RoleType::SuperAdmin->value, 'web')
            ->syncPermissions(Permission::query()->pluck('name'));

        Role::findByName(RoleType::Administrator->value, 'web')
            ->syncPermissions(Permission::query()->pluck('name'));

        Role::findByName(RoleType::Staff->value, 'web')
            ->syncPermissions([
                'users.view',
                'roles.view',
                'permissions.view',
                'settings.view',
                'navigation.view',
                'feature-flags.view',
                'company.view',
                'media.view',
                'services.view',
                'projects.view',
            ]);

        Role::findByName(RoleType::Client->value, 'web')
            ->syncPermissions([]);
    }
}
