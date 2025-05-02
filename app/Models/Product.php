<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'harga',
        'hargajual',
        'stock',
        'remark',
        'image'
    ];

    // Relasi dengan StockRequest
    public function stockRequests()
    {
        return $this->hasMany(StockRequest::class);
    }
}
