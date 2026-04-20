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
    Schema::create('sensor_data', function (Blueprint $table) {
        $table->id();
        $table->json('biopond'); // Simpan array 6 loadcell dalam format JSON
        $table->integer('harvest');
        $table->float('temp');
        $table->float('hum');
        $table->json('soil');
        $table->integer('ammonia');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensor_data');
    }
};
