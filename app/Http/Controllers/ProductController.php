<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/ProductIndex', [
            'products' => Product::orderBy('created_at', 'desc')->get(),
        ]);
    }
    public function api(Request $request)
    {
        $query = Product::query();

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $query->orderBy('id', 'desc');

        $products = $query->paginate($request->per_page ?? 10);

        return response()->json($products);
    }

    public function getProducts(Request $request)
    {
        $query = Product::query();

        // Apply search
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Apply sorting
        if ($request->has('order')) {
            $orderColumn = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];
            $columns = ['name', 'code', 'harga', 'stock', 'remark'];
            $query->orderBy($columns[$orderColumn], $orderDirection);
        }

        // Apply pagination
        $products = $query->paginate($request->length);

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => Product::count(),
            'recordsFiltered' => $products->total(),
            'data' => $products->items(),
        ]);
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        return Inertia::render('Admin/ProductCreate', [
            'products' => $products,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:products,code',
            'harga' => 'required|integer|min:0',
            'hargajual' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
            'remark' => 'nullable|string',
            'image' => 'nullable|image|max:10048', // maksimal 2MB
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->back();
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required',
            'stock' => 'required|integer|min:0',
            'harga' => 'required|integer|min:0',
            'hargajual' => 'required|integer|min:0',
            'remark' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Hapus gambar lama jika ada
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->back();
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->back();
    }
}
