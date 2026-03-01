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
        Schema::table('content_requests', function (Blueprint $table) {
            $table->integer('impressions_count')->nullable()->after('reach_count');
            $table->integer('saved_count')->nullable()->after('engagement_count');
            $table->integer('video_views_count')->nullable()->after('saved_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_requests', function (Blueprint $table) {
            $table->dropColumn(['impressions_count', 'saved_count', 'video_views_count']);
        });
    }
};
