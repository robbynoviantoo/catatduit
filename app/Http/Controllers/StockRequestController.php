<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\Product;
use App\Models\ProfitHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class StockRequestController extends Controller
{
    public function index()
    {
        $requests = StockRequest::with('product', 'user')->latest()->get();
        return Inertia::render('Admin/StockRequestIndex', compact('requests'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        StockRequest::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->back();
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();

        return Inertia::render('User/StockRequestCreate', [
            'products' => $products,
        ]);
    }

    public function approve(StockRequest $stockRequest)
    {
        Log::info('Memproses approve request', ['request_id' => $stockRequest->id]);
    
        $stockRequest->load('product');
        $product = $stockRequest->product;
    
        if (!$product) {
            Log::warning('Produk tidak ditemukan untuk request', ['request_id' => $stockRequest->id]);
            return back()->withErrors(['error' => 'Produk tidak ditemukan']);
        }
    
        if ($product->stock < $stockRequest->quantity) {
            Log::warning('Stok tidak cukup', [
                'product_id' => $product->id,
                'stok' => $product->stock,
                'diminta' => $stockRequest->quantity
            ]);
            return back()->withErrors(['error' => 'Stok tidak cukup']);
        }
    
        // Mengurangi stok produk
        $product->decrement('stock', $stockRequest->quantity);
        $stockRequest->update(['status' => 'approved']);
    
        // Menghitung keuntungan
        $profit = ($product->hargajual - $product->harga) * $stockRequest->quantity;
    
        // Menyimpan history keuntungan
        ProfitHistory::create([
            'product_id' => $product->id,
            'stock_request_id' => $stockRequest->id,
            'quantity' => $stockRequest->quantity,
            'harga_beli' => $product->harga,
            'harga_jual' => $product->hargajual,
            'profit' => $profit,
        ]);
    
        Log::info('Permintaan disetujui dan stok dikurangi', [
            'product_id' => $product->id,
            'sisa_stok' => $product->fresh()->stock,
            'request_id' => $stockRequest->id,
        ]);
    
        return redirect()->back()->with('success', 'Permintaan disetujui.');
    }
    
    

    public function reject(StockRequest $stockRequest)
    {
        $stockRequest->update(['status' => 'rejected']);
        return redirect()->back();
    }
}
