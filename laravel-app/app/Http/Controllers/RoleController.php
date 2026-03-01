<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Role/Index', [
            'roles' => Role::with('permissions')->get(),
            'permissions' => Permission::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
        ]);

        Role::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, Role $role)
    {
        if ($role->name === 'Super Admin' && !auth()->user()->hasRole('Super Admin')) {
            return redirect()->back()->with('error', 'Hanya Super Admin yang bisa mengubah Role Super Admin.');
        }

        $request->validate([
            'permissions' => 'required|array',
        ]);

        $role->syncPermissions($request->permissions);

        return redirect()->back()->with('success', 'Hak akses role berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return redirect()->back()->with('error', 'Role Super Admin tidak dapat dihapus.');
        }

        // Optional: Check if role is in use
        if ($role->users()->exists()) {
            return redirect()->back()->with('error', 'Role masih digunakan oleh beberapa user. Hapus atau pindahkan user terlebih dahulu.');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }
}
