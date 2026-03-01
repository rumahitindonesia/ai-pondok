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
        Schema::create('content_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->onDelete('set null');
            $table->string('requester_name');
            $table->string('phone_number');
            $table->string('format'); // e.g., Video Editor, Design, etc.
            $table->date('deadline_date');
            $table->time('deadline_time');
            $table->string('size'); // e.g., Potrait 9:16, Landscape 16:9, etc.
            $table->string('output_format'); // e.g., Jpg, Mp4
            $table->boolean('include_logo')->default(false);
            $table->boolean('include_website')->default(false);
            $table->boolean('include_social_media')->default(false);
            $table->boolean('include_phone')->default(false);
            $table->text('content_description');
            $table->text('special_notes')->nullable();
            $table->string('reference_link')->nullable();
            $table->string('status')->default('Menunggu Assign');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content_requests');
    }
};
