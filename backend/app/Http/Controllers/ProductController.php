<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService){}
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::with('user')->orderByDesc('created_at');

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;

            // Adjust the fields you want to search in
            $query->where(function ($q) use ($search) {
                $q->where('sku_code', 'like', "%{$search}%")
                ->orWhere('sku_desc', 'like', "%{$search}%")
                ->orWhere('sku_uom', 'like', "%{$search}%");
            });
        }

        return $query->paginate(5);
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
            'user_id' => 'required|exists:users,id',
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
        if(!$this->productService->create($request->only(["user_id", "sku_code", "sku_desc", "sku_desc_long", "sku_uom", "sku_price"]))) {
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
