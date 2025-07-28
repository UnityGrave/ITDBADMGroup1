<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:Customer,Employee,Admin'
        ]);

        try {
            DB::beginTransaction();

            // Create the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'email_verified_at' => now(), // Auto-verify admin created users
            ]);

            // Assign role
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->attach($role->id);
            }

            // Log user activity
            DB::table('user_activity_log')->insert([
                'user_id' => auth()->id(),
                'activity_type' => 'user_create',
                'description' => "Created new user: {$user->name} ({$user->email}) with role: {$request->role}",
                'related_table' => 'users',
                'related_id' => $user->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user' => $user->load('roles')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|in:Customer,Employee,Admin'
        ];

        // Only validate password if it's provided
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Store old values for logging
            $oldData = [
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name')->toArray()
            ];

            // Update user basic info
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            // Update role
            $role = Role::where('name', $request->role)->first();
            if ($role) {
                $user->roles()->sync([$role->id]);
            }

            // Log user activity
            $changes = [];
            if ($oldData['name'] !== $request->name) {
                $changes[] = "name: '{$oldData['name']}' → '{$request->name}'";
            }
            if ($oldData['email'] !== $request->email) {
                $changes[] = "email: '{$oldData['email']}' → '{$request->email}'";
            }
            if (!in_array($request->role, $oldData['roles'])) {
                $changes[] = "role: '" . implode(',', $oldData['roles']) . "' → '{$request->role}'";
            }
            if ($request->filled('password')) {
                $changes[] = "password updated";
            }

            if (!empty($changes)) {
                DB::table('user_activity_log')->insert([
                    'user_id' => auth()->id(),
                    'activity_type' => 'user_update',
                    'description' => "Updated user {$user->name}: " . implode(', ', $changes),
                    'related_table' => 'users',
                    'related_id' => $user->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'user' => $user->fresh()->load('roles')
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent deleting the current user
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.'
            ], 403);
        }

        // Prevent deleting the last admin
        if ($user->hasRole('Admin')) {
            $adminCount = User::whereHas('roles', function ($query) {
                $query->where('name', 'Admin');
            })->count();

            if ($adminCount <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete the last admin user.'
                ], 403);
            }
        }

        try {
            DB::beginTransaction();

            // Store user info for logging
            $userName = $user->name;
            $userEmail = $user->email;
            $userRoles = $user->roles->pluck('name')->toArray();

            // Log user activity before deletion
            DB::table('user_activity_log')->insert([
                'user_id' => auth()->id(),
                'activity_type' => 'user_delete',
                'description' => "Deleted user: {$userName} ({$userEmail}) with roles: " . implode(',', $userRoles),
                'related_table' => 'users',
                'related_id' => $user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now()
            ]);

            // Delete the user (this will cascade to role assignments)
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the user.'
            ], 500);
        }
    }
}
