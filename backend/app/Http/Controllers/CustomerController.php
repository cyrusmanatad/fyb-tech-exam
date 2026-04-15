<?php

namespace App\Http\Controllers;

use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->select(['id', 'name', 'email', 'created_at', 'is_active', 'last_login_at'])
            ->with([
                'orders:id,user_id,total,status,created_at',
            ])
            ->whereDoesntHave('roles')
            ->orderByDesc('last_login_at');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            match($request->status) {
                'active'   => $query->where('is_active', true),
                'inactive' => $query->where('is_active', false),
                'new'      => $query->where('created_at', '>=', now()->subDays(7)),
                default    => null,
            };
        }

        return CustomerResource::collection($query->paginate($request->per_page ?? 10));
    }

    public function show(User $user)
    {
        $user->load([
            'orders:id,user_id,order_number,total,status,payment_status,created_at',
            'orders.items:id,order_id,product_name,sku,quantity,final_price,subtotal',
        ]);

        return new CustomerResource($user);
    }

    public function total()
    {
        $totalCustomers = User::whereDoesntHave('roles')->count();

        // New customers in the last 7 days
        $newCustomers = User::whereDoesntHave('roles')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        // Active — logged in within last 30 days
        $activeCustomers = User::whereDoesntHave('roles')
            ->where('last_login_at', '>=', now()->subDays(30))
            ->count();

        // Retention — customers who placed more than 1 order
        $retainedCustomers = User::whereDoesntHave('roles')
            ->withCount('orders')
            ->having('orders_count', '>', 1) // ✅ correct way
            ->get()
            ->count();

        // Retention rate — percentage of customers with more than 1 order
        $retentionRate = $totalCustomers > 0
            ? round(($retainedCustomers / $totalCustomers) * 100, 1)
            : 0;

        return response()->json([
            'data' => [
                'total' => $totalCustomers,
                'new'            => $newCustomers,
                'active'         => $activeCustomers,
                'retention'      => $retentionRate, // percentage e.g. 42.5
                'retained'       => $retainedCustomers,  // raw count
            ]
        ]);
    }
}
