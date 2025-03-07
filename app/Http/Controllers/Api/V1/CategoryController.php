<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\updateCategoryRequest;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            
            $categories = Cache::remember('categories', 60, function() use ($perPage){
                return Category::orderBy('created_at', 'desc')->paginate($perPage);
            });

            return response()->json([
                'categories' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            $data = $request->validated();
            Category::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'status' => 1
            ]);

            //clear cache
            Cache::forget('categories');

            return response()->json([
                'message' => 'Category created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $category = Cache::remember('category', 60, function() use ($id){
                return Category::findOrFail($id);
            });

            return response()->json([
                'category' => $category
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(updateCategoryRequest $request, string $id)
    {
        try {
            $data = $request->validated();
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'status' => $data['status']
            ]);

            //clear cache
            Cache::forget('categories');
            Cache::forget('category');

            return response()->json([
                'message' => 'Category updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::findOrFail($id);
            $category->delete();

            //clear cache
            Cache::forget('categories');
            Cache::forget('category');

            return response()->json([
                'message' => 'Category deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }
}
