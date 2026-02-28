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
        Schema::create('pengurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatans')->cascadeOnDelete();
            // Status aktif/tidak menjabat (bisa untuk track history jabatan)
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            // Opsional: Pastikan kombinasi unik untuk jabatan aktif
            // $table->unique(['santri_id', 'jabatan_id', 'status']); // atau via model logic nanti
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengurus');
    }
};
