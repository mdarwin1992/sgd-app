<?php

namespace App\Http\Controllers\dashboard\user;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::with(['roles', 'permissions'])->get();
            return response()->json([
                'status' => 'success',
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener los usuarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function list()
    {
        // Obtener los usuarios que tienen el rol 'USUARIO' y que no están asignados a ninguna oficina
        $users = User::role('USUARIO')
            ->whereDoesntHave('offices') // Asume que tienes una relación 'offices' en el modelo User
            ->get();

        // Retornar una respuesta JSON con los datos obtenidos y un mensaje de éxito
        return response()->json([
            'data' => $users,
            'message' => 'Users successfully recovered'
        ]);
    }

    public function show($id)
    {
        try {
            $user = User::with(['roles', 'permissions'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Usuario no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:20',
                'roles' => 'required|string',
                'permissions' => 'array'
            ]);

            DB::beginTransaction();

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'phone' => $validated['phone'] ?? null,
            ]);

            // Asignar roles al usuario
            if (!empty($validated['roles'])) {
                $role = Role::find($validated['roles']);
                if ($role) {
                    $user->assignRole($role->name);
                }
            }

            // Asignar permisos al usuario
            if (!empty($validated['permissions'])) {

                foreach ($validated['permissions'] as $permissions) {
                    $permissions = Permission::find($permissions);
                    $user->givePermissionTo($permissions->name);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario creado exitosamente',
                'data' => $user->load(['roles', 'permissions'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => [
                    'sometimes',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => 'sometimes|string|min:8',
                'phone' => 'nullable|string|max:20',
                'roles' => 'array',
                'permissions' => 'array'
            ]);

            DB::beginTransaction();

            if (isset($validated['name'])) {
                $user->name = $validated['name'];
            }
            if (isset($validated['email'])) {
                $user->email = $validated['email'];
            }
            if (isset($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            if (isset($validated['phone'])) {
                $user->phone = $validated['phone'];
            }

            $user->save();

            // Asignar roles al usuario
            if (!empty($validated['roles'])) {
                $role = Role::find($validated['roles']);
                if ($role) {
                    $user->syncRoles($role->name);
                }
            }

            // Asignar permisos al usuario
            if (!empty($validated['permissions'])) {

                foreach ($validated['permissions'] as $permissions) {
                    $permissions = Permission::find($permissions);
                    $user->syncPermissions($permissions->name);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario actualizado exitosamente',
                'data' => $user->load(['roles', 'permissions'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
                $adminCount = User::role(['admin', 'super-admin'])->count();
                if ($adminCount <= 1) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'No se puede eliminar el último usuario administrador'
                    ], 422);
                }
            }

            DB::beginTransaction();

            $user->roles()->detach();
            $user->permissions()->detach();

            $user->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar el usuario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRoles()
    {
        try {
            $roles = Role::with('permissions')->get();
            return response()->json([
                'status' => 'success',
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener los roles',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPermissions()
    {
        try {
            $permissions = Permission::all();
            return response()->json([
                'status' => 'success',
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al obtener los permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getUserPermissionsById($id)
    {
        try {
            // Buscar al usuario por ID
            $user = User::findOrFail($id);

            // Obtener los permisos del usuario
            $permissions = $user->getAllPermissions();

            return response()->json($permissions, 200);
        } catch (\Exception $e) {
            \Log::error('Error al obtener permisos del usuario: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudieron obtener los permisos del usuario'], 500);
        }
    }

    public function checkRoleHasPermissions($id)
    {
        try {
            $role = Role::findOrFail($id);
            $hasPermissions = $role->permissions()->count() > 0;

            return response()->json($hasPermissions);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al verificar permisos del rol',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
