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
        if (!Schema::hasColumn('camera_orders', 'is_sold')) {
            Schema::table('camera_orders', function (Blueprint $table) {
                $table->boolean('is_sold')->default(true)->comment('Đã bán được hay chưa')->after('order_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('camera_orders', 'is_sold')) {
            Schema::table('camera_orders', function (Blueprint $table) {
                $table->dropColumn('is_sold');
            });
        }
    }
};
