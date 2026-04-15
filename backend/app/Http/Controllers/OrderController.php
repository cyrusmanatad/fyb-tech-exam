<?php

namespace App\Http\Controllers;

use App\DTOs\OrderData;
use App\Enums\OrderStatus;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrdersTransactionResource;
use App\Models\Order;
use App\Services\OrderService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use \Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Order::query()
        ->select(['id','user_id','order_number','total','status','payment_status','payment_method','created_at'])
        ->with([
            'user:id,name',
            'items:id,order_id,sku,product_name,quantity,subtotal'
        ])
        ->orderByDesc('created_at');

        if($request->filled('status')){
            $query->where('status', $request->status);
        }

        if($request->filled('search')){
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                ->orWhereHas("user", function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%");
                });
            });
        }

        return OrdersTransactionResource::collection($query->paginate(10)); // or dynamic apprch $request->per_page
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        try {

            $dto = OrderData::fromRequest(
                $request->validated(),
                Auth::id()
            );
            $order = $this->orderService->create($dto);

            return response()->json([
                "message" => "Order created successfully",
                "data" => $order
            ], 201);

        } catch (ValidationException $e) {
            throw $e; // re-throw — Laravel handles 422 response

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to create order',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        try {
            $order = $order->update($request->validated());

            return response()->json([
                "message" => "Order updated successfully",
                "data" => $order
            ], 201);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to update order',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order = $order->delete();

        return response()->json([
            "message" => "Order updated successfully",
            "data" => $order
        ], 201);
    }

    /**
     * Stats
     */
    public function total()
    {
        return response()->json([
            'data' => [
                'today' => Order::whereDate('created_at', now())->count(),
                'total' => Order::count(),
                'pending' => Order::where('status', OrderStatus::PENDING)->count(),
                'completed' => Order::whereIn('status', [OrderStatus::SHIPPED,OrderStatus::DELIVERED])->count(),
                'revenue' => Order::sum('total'),
            ]
        ]);
    }

    public function export(Request $request)
    {
        $orders = Order::with('user')
            ->orderByDesc('created_at')
            ->get();

        $pdf = Pdf::loadView('reports.orders', compact('orders'))
            ->setPaper('a4', 'landscape');

        // view pdf file
        return $pdf->stream('orders-report.pdf');
        // or download
        return $pdf->download('orders-report.pdf');

    }
}
