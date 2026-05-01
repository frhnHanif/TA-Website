<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cycles', function (Blueprint $table) {
            $table->id();
            $table->string('batch_id')->unique(); // Contoh: #BCH-20260401
            $table->timestamp('start_date')->useCurrent();
            $table->timestamp('end_date')->nullable();
            $table->decimal('initial_seed_mass', 8, 2); // dalam Gram
            $table->decimal('total_waste_input', 8, 2)->default(0); // dalam Kg
            $table->decimal('harvest_mass', 8, 2)->nullable(); // dalam Kg
            $table->decimal('residue_mass', 8, 2)->nullable(); // Kasgot dalam Kg
            $table->decimal('wri_result', 8, 2)->nullable(); // % per hari
            $table->decimal('eci_result', 8, 2)->nullable(); // %
            $table->enum('status', ['berjalan', 'selesai'])->default('berjalan');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cycles');
    }
};