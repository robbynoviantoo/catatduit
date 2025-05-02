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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date'); // tanggal transaksi
            $table->enum('type', ['income', 'expense']); // masuk atau keluar
            $table->decimal('amount', 15, 2); // nominal uang
            $table->string('category'); // kategori transaksi
            $table->text('remark')->nullable(); // catatan tambahan
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // yang input
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
