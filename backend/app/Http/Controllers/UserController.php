<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request)
    {
        $query = User::query()
        ->select(['id','name','email','created_at', 'last_login_at'])
        ->with([
            'roles:id,name'
        ])
        ->whereHas('roles')
        ->orderByDesc("last_login_at");
        
        if($request->filled('search')){
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas("roles", function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }
        
        return UserResource::collection($query->paginate(10));
    }

    public function store(StoreUserRequest $request){
        try {
            $result = $this->userService->create($request->validated());

            $user          = $result['user'];
            $plainPassword = $result['password'];

            // Send welcome email with generated password
            // $user->notify(new WelcomeUserNotification($plainPassword));

            return response()->json([
                'message' => 'User created successfully',
                'data'    => new UserResource($user),
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function update_status(Request $request, User $user)
    {
        // Pass $user as second argument
        $this->authorize('update', $user);

        $request->validate([
            'is_active' => ['required', 'integer'],
        ]);

        // Prevent deactivating yourself
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot change your own status.'
            ], 422);
        }

        $user->update([
            'is_active' => $request->is_active
        ]);

        return response()->json([
            'message' => 'User status updated successfully',
            'user' => $user,
            'is_active' => $request->is_active
        ]);
    }

    public function update_role(Request $request, User $user)
    {
        $request->validate([
            'role' => [
                'required',
                'string',
                'exists:roles,name',
            ],
        ]);

        // Prevent changing Super Admin role unless you are Super Admin
        if (
            $user->hasRole('Super Admin') &&
            !auth()->user()->hasRole('Super Admin')
        ) {
            return response()->json([
                'message' => 'You are not authorized to change a Super Admin role.'
            ], 403);
        }

        // Prevent assigning Super Admin role unless you are Super Admin
        if (
            $request->role === 'Super Admin' &&
            !auth()->user()->hasRole('Super Admin')
        ) {
            return response()->json([
                'message' => 'You are not authorized to assign Super Admin role.'
            ], 403);
        }

        // Prevent changing your own role
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'You cannot change your own role.'
            ], 403);
        }

        // Sync role — removes old role and assigns new one
        $user->syncRoles([$request->role]);

        return response()->json([
            'message' => 'User role updated successfully',
            'data'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'roles' => $user->getRoleNames(),
            ]
        ]);
    }

    public function destroy(Request $request, User $user)
    {
        // Authorization
        if (!$request->user()->hasPermissionTo('delete users')) {
            return response()->json([
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Prevent self-deletion
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'You cannot delete your own account.'
            ], 422);
        }

        // Prevent deleting Super Admin unless you are Super Admin
        if (
            $user->hasRole('Super Admin') &&
            !$request->user()->hasRole('Super Admin')
        ) {
            return response()->json([
                'message' => 'You are not authorized to delete a Super Admin.'
            ], 403);
        }

        try {
            DB::transaction(function () use ($user) {
                // Detach roles first, then delete
                // Only needed if cascade is NOT set on model_has_roles
                $user->roles()->detach();            
                $user->delete();            // soft delete if model uses SoftDeletes
            });

            return response()->json([
                'message' => 'User deleted successfully'
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to delete user',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function total()
    {
        $roleCounts = Role::withCount('users')->get()
        ->mapWithKeys(fn($role) => [
            $role->name => $role->users_count
        ]);
        return response()->json([
            'data' => [
                'total'=> User::role(['Admin', 'Super Admin','Support','Inventory Staff'])->count(),
                'admin' => User::role(['Admin', 'Super Admin'])->count(),
                'non_admin' => User::role(['Support', 'Inventory Staff'])->count(),
                'active' => User::count(),
                'by_role' => $roleCounts // standby not yet used
            ]
        ]);
    }
}