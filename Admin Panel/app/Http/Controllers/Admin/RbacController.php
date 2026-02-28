<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\RbacService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * RbacController — Admin Panel Role & Permission Management
 *
 * Thin HTTP layer: validates input, delegates to RbacService for all
 * business logic, and returns views or redirects.
 *
 * Routes are declared in routes/panels/admin.php under:
 *   /admin/role/*         — role CRUD
 *   /admin/permissions/*  — permission CRUD + role sync
 */
class RbacController extends Controller
{
    public function __construct(
        private readonly RbacService $rbac
    ) {}

    // ──────────────────────────────────────────────────────────────────────────
    // ROLE LISTING
    // ──────────────────────────────────────────────────────────────────────────

    public function index(): View
    {
        $roles = $this->rbac->allRoles(withPermissions: true);

        return view('admin.rbac.roles.index', compact('roles'));
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ROLE CREATE / STORE
    // ──────────────────────────────────────────────────────────────────────────

    public function save(): View
    {
        $permissions = $this->rbac->allPermissions(grouped: true);

        return view('admin.rbac.roles.save', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'role_name'     => 'required|string|max:100|unique:role,role_name',
            'is_active'     => 'boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $role = $this->rbac->createRole($data);

        if (! empty($data['permissions'])) {
            $this->rbac->syncRolePermissions($role->id, $data['permissions']);
        }

        return redirect()->route('admin.role.index')
            ->with('success', "Role \"{$role->role_name}\" created successfully.");
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ROLE EDIT / UPDATE
    // ──────────────────────────────────────────────────────────────────────────

    public function edit(int $id): View
    {
        $role            = $this->rbac->findRole($id);
        $permissions     = $this->rbac->allPermissions(grouped: true);
        $assignedIds     = $this->rbac->rolePermissionIds($id);
        $usersCount      = \App\Models\User::where('role_id', $id)->count();

        return view('admin.rbac.roles.edit', compact('role', 'permissions', 'assignedIds', 'usersCount'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'role_name'     => "required|string|max:100|unique:role,role_name,{$id}",
            'is_active'     => 'boolean',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $this->rbac->updateRole($id, $data);
        $this->rbac->syncRolePermissions($id, $data['permissions'] ?? []);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role updated successfully.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ROLE DELETE
    // ──────────────────────────────────────────────────────────────────────────

    public function delete(int $id): RedirectResponse
    {
        $this->rbac->deleteRole($id);

        return redirect()->route('admin.role.index')
            ->with('success', 'Role deleted. Affected staff users have been unassigned.');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // PERMISSION MANAGEMENT
    // ──────────────────────────────────────────────────────────────────────────

    public function permissions(): View
    {
        // Flat collection with roles count for the table
        $permissions = \App\Models\Permission::withCount('roles')
            ->orderBy('group')
            ->orderBy('display_name')
            ->get();

        // Unique group names for the filter dropdown and datalist
        $groups = $permissions->pluck('group')->unique()->sort()->values()->toArray();

        return view('admin.rbac.permissions.index', compact('permissions', 'groups'));
    }

    public function storePermission(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100|unique:permissions,name',
            'group'        => 'required|string|max:100',
            'display_name' => 'required|string|max:150',
        ]);

        $this->rbac->createPermission($data);

        return redirect()->route('admin.permissions.index')
            ->with('success', "Permission \"{$data['name']}\" created.");
    }

    public function updatePermission(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'name'         => "nullable|string|max:100|unique:permissions,name,{$id}",
            'group'        => 'nullable|string|max:100',
            'display_name' => 'nullable|string|max:150',
        ]);

        $this->rbac->updatePermission($id, array_filter($data));

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated.');
    }

    public function destroyPermission(int $id): RedirectResponse
    {
        $this->rbac->destroyPermission($id);

        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted and detached from all roles.');
    }

    /**
     * Sync the full permission set for a role (AJAX-friendly).
     * Called from the role edit form checkboxes.
     */
    public function syncRolePermissions(Request $request, int $roleId): RedirectResponse
    {
        $data = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $this->rbac->syncRolePermissions($roleId, $data['permissions'] ?? []);

        return redirect()->route('admin.role.edit', $roleId)
            ->with('success', 'Permissions updated.');
    }
}
