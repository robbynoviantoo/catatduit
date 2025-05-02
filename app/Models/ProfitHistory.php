<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'stock_request_id',
        'quantity',
        'harga_beli',
        'harga_jual',
        'profit',
    ];

    // Relasi dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi dengan StockRequest
    public function stockRequest()
    {
        return $this->belongsTo(StockRequest::class);
    }
}
