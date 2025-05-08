<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Xóa tất cả user hiện tại trước khi thêm mới (đề phòng có sẵn)
        DB::table('users')->truncate();
        
        // Thêm user admin với mật khẩu không mã hóa
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@camerashop.com',
            'password' => '123456', // Mật khẩu không mã hóa
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', 'admin@camerashop.com')->delete();
    }
};
