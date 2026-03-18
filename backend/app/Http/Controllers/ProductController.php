<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query()
        ->select(['id','user_id','slug', 'status', 'created_at'])
        ->with([
            'user:id,name,email',
            'variants:id,product_id,desc,sku,uom,price,sale_price,currency',
            'variants.inventory:id,variant_id,stock_quantity,reserved_quantity',
            'category:id,name'
        ])
        ->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('slug', 'like', "%{$search}%")
                ->orWhereHas("variants", function ($q2) use ($search) {
                    $q2->where('sku', 'like', "%{$search}%")
                    ->orWhere('desc', 'like', "%{$search}%")
                    ->orWhere('uom', 'like', "%{$search}%");
                });
            });
        }

        return ProductResource::collection($query->paginate(10));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sku_code' => 'required|string|unique:products,sku_code',
            'sku_desc' => 'required|string',
            'sku_uom' => 'required|string',
            'sku_price' => 'required|numeric',
        ]);

        if($validator->fails()) {
            return response()->json([
                "message" => "Unable to create product",
                "errors" => $validator->errors()
            ], 422);
        }

        $data = $request->only(["sku_code", "sku_desc", "sku_desc_long", "sku_uom", "sku_price"]);

        $data['user_id'] = Auth::id();

        if(!$this->productService->create($data)) {
            return response()->json([
                "message" => "Failed to create product"
            ], 500);
        }
        
        return response()->json([
            "message" => "Product created successfully"
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return response()->json($product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        if(!$this->productService->update($product, $request->validated())) {
            return response()->json([
                "message" => "Failed to update product"
            ], 500);
        }
        
        return response()->json([
            "message" => "Product updated successfully"
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->productService->delete($product);

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

}
