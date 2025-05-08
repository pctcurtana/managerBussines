<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('camera_orders', function (Blueprint $table) {
            $table->id();
            $table->string('camera_name');
            $table->decimal('cost_price', 15, 2)->comment('Giá thu lại');
            $table->decimal('selling_price', 15, 2)->comment('Giá bán đi');
            $table->decimal('profit', 15, 2)->comment('Tiền lời');
            $table->date('order_date');
            $table->boolean('is_sold')->default(true)->comment('Đã bán được hay chưa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camera_orders');
    }
}; 