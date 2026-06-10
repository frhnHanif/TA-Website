<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_controls', function (Blueprint $table) {
            // Menyimpan ID User yang mengunci (bisa kosong/null jika mode otomatis)
            $table->foreignId('controlled_by')->nullable()->constrained('users')->onDelete('set null');
            // Menyimpan timestamp batas waktu kunci berakhir
            $table->timestamp('locked_until')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('device_controls', function (Blueprint $table) {
            $table->dropForeign(['controlled_by']);
            $table->dropColumn(['controlled_by', 'locked_until']);
        });
    }
};