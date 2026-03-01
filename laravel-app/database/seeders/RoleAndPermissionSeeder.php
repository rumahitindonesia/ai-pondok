<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $modules = ['santri', 'finance', 'psb', 'materi', 'attendance', 'discipline', 'users', 'organisasi', 'homepage', 'content manager', 'content staff', 'content request'];
        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$action $module"]);
            }
        }

        // Create roles and assign existing permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        // Super admin gets all permissions via Gate::before in AuthServiceProvider OR just assign all here
        $superAdmin->syncPermissions(Permission::all());

        $pengurus = Role::firstOrCreate(['name' => 'Pengurus']);
        $pengurus->syncPermissions(Permission::where('name', 'not like', '%users')->get());

        Role::firstOrCreate(['name' => 'Media Manager']);
        Role::firstOrCreate(['name' => 'Media Staff']);

        // Assign Super Admin to first user
        $user = User::first();
        if ($user) {
            $user->assignRole($superAdmin);
        }
    }
}
