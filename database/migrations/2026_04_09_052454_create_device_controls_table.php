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
    Schema::create('device_controls', function (Blueprint $table) {
        $table->id();
        $table->boolean('is_manual')->default(0);
        $table->json('mist'); // Simpan array [1,0,1,0,0,0]
        $table->integer('fan')->default(0); // Kecepatan 0-100
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_controls');
    }
};
