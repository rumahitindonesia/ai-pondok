<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE form_questions MODIFY COLUMN type ENUM('text', 'textarea', 'radio', 'checkbox', 'file', 'date', 'select') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE form_questions MODIFY COLUMN type ENUM('text', 'textarea', 'radio', 'checkbox', 'file', 'date') NOT NULL");
        }
    }
};
