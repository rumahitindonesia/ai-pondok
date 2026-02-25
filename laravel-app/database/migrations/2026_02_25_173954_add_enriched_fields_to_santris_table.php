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
        Schema::table('santris', function (Blueprint $table) {
            $table->text('bio')->nullable()->after('alamat');
            $table->string('minat_bakat')->nullable()->after('bio');
            $table->string('cita_cita')->nullable()->after('minat_bakat');
            $table->string('email')->nullable()->after('cita_cita');
            $table->string('no_hp')->nullable()->after('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('santris', function (Blueprint $table) {
            $table->dropColumn(['bio', 'minat_bakat', 'cita_cita', 'email', 'no_hp']);
        });
    }
};
