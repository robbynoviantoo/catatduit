<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profit_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('stock_request_id')->constrained()->onDelete('cascade');
            $table->integer('quantity'); // Jumlah produk yang diproses
            $table->decimal('harga_beli', 10, 2); // Harga beli
            $table->decimal('harga_jual', 10, 2); // Harga jual
            $table->decimal('profit', 10, 2); // Keuntungan (harga jual - harga beli) * quantity
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('profit_histories');
    }
};
