<?php

namespace App\Http\Controllers\Api;

use App\Category;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiProductController extends Controller
{
    /**
     * Get all products with pagination
     * GET /api/products
     */
    public function index(Request $request)
    {
        $query = Product::query();
        
        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search by name or code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('mansx', 'like', "%{$search}%");
            });
        }
        
        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        // Sorting
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Pagination
        $perPage = $request->get('per_page', 12);
        $products = $query->with('categories')->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }
    
    /**
     * Get single product by ID
     * GET /api/products/{id}
     */
    public function show($id)
    {
        $product = Product::with('categories')->find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }
    
    /**
     * Get featured products (latest updated)
     * GET /api/products/featured
     */
    public function featured()
    {
        $products = Product::with('categories')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    
    /**
     * Get products by category
     * GET /api/categories/{id}/products
     */
    public function byCategory($categoryId, Request $request)
    {
        $category = Category::find($categoryId);
        
        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }
        
        $perPage = $request->get('per_page', 12);
        $products = Product::where('category_id', $categoryId)
            ->with('categories')
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'category' => $category,
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]
        ]);
    }
}
