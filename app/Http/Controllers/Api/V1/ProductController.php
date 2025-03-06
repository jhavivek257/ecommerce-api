<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $products = Product::orderBy('created_at', 'desc')->paginate($perPage);
            return response()->json([
                'data' => $products
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateProductRequest $request)
    {
        try {
            $data = $request->validated();

            // Store multiple images
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('products', 'public');
                }
            }

            Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'sku' => $data['sku'],
                'mrp' => $data['mrp'],
                'salePrice' => $data['salePrice'],
                'stock' => $data['stock'],
                'description' => $request->description,
                'thumbnail' => $request->file('thumbnail')->store('products', 'public'),
                'images' => json_encode($imagePaths),
                'catId' => $data['catId'],
                'status' => 1
            ]);

            return response()->json([
                'message' => "Product created successfully",
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    "message" => "Product not found"
                ], 404);
            }
            return response()->json([
                'data' => $product
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    "message" => "Product not found"
                ], 404);
            }

            //handle thumbnail
            if($request->hasFile('thumbnail')) {
                if ($product->thumbnail) {
                    Storage::disk('public')->delete($product->thumbnail);
                }
                $product->thumbnail = $request->file('thumbnail')->store('products', 'public');
            }

            //handle images
            if($request->hasFile('images')) {
                $images = json_decode($product->images, true);
                if (!empty($images)) {
                    foreach ($images as $image) {
                        Storage::disk('public')->delete($image);
                    }
                }
                $imagePaths = [];
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('products', 'public');
                }
                $product->images = json_encode($imagePaths);
            }

            //update product
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'sku' => $request->sku,
                'thumbnail' => $product->thumbnail,
                'images' => $product->images,
                'mrp' => $request->mrp,
                'salePrice' => $request->salePrice,
                'stock' => $request->stock,
                'description' => $request->description,
                'catId' => $request->catId,
                'status' => $request->status
            ]);
            return response()->json([
                'message' => 'Product updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $product = Product::find($id);
            if (!$product) {
                return response()->json([
                    "message" => "Product not found"
                ], 404);
            }

            if ($product->thumbnail) {
                Storage::disk('public')->delete($product->thumbnail);
            }

            $images = json_decode($product->images, true);
            
            if (!empty($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            $product->delete();
            return response()->json([
                'message' => 'Product deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
