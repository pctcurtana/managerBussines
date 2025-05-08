<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm user mặc định cho ứng dụng
        User::create([
            'name' => 'Admin',
            'email' => 'admin@camerashop.com',
            'password' => '123456', // Mật khẩu không được mã hóa
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        User::where('email', 'admin@camerashop.com')->delete();
    }
};
