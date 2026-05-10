<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource (Admin).
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', $search);
        }

        $products = $query->get();
        return view('admin.products.index', compact('products'));
    }

     
    public function shop(Request $request)
    {
        // [SECURITY: SQL INJECTION PROTECTION]
        // UNSAFE EXAMPLE: $products = \Illuminate\Support\Facades\DB::select("SELECT * FROM products WHERE name LIKE '%$search%'");
        // PROTECTED VERSION: Using Eloquent ORM which uses PDO parameter binding securely.
        $query = Product::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        $products = $query->get();
        
        return view('shop.index', compact('products'));
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ SECURE VERSION (FOR EXPLANATION ONLY)
    |--------------------------------------------------------------------------
    |
    | public function shop(Request $request)
    | {
    |     $query = Product::query();
    |
    |     if ($request->has('search')) {
    |         $search = $request->get('search');
    |
    |         $query->where('name', 'like', "%{$search}%")
    |               ->orWhere('description', 'like', "%{$search}%");
    |     }
    |
    |     $products = $query->get();
    |     return view('shop.index', compact('products'));
    | }
    |
    */

    /**
     * Show single product
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('shop.show', compact('product'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * Store product
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'required|image|mimes:jpg,png|max:2048',
        ]);

        $file = $request->file('image');
        $name = time() . '.' . $file->getClientOriginalExtension();
        
        // Resize image using Intervention Image
        $image = \Intervention\Image\Laravel\Facades\Image::read($file);
        $image->cover(800, 800); // Resize and crop to 800x800
        
        // Save to storage
        $imagePath = 'products/' . $name;
        Storage::disk('public')->put($imagePath, (string) $image->encodeByExtension($file->getClientOriginalExtension()));

        Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product added successfully.');
    }

    /**
     * Edit product
     */
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpg,png|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
        ];

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete product
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
