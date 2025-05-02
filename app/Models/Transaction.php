<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'amount',
        'category',
        'remark',
        'user_id',
    ];

    // Relasi ke user yang membuat transaksi (jika diperlukan)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
