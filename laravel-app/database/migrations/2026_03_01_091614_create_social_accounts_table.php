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
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('platform')->default('instagram');
            $table->string('platform_user_id')->nullable();
            $table->text('access_token');
            $table->dateTime('token_expires_at')->nullable();
            $table->string('instagram_username')->nullable();
            $table->string('instagram_business_id')->nullable();
            $table->string('facebook_page_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_accounts');
    }
};
