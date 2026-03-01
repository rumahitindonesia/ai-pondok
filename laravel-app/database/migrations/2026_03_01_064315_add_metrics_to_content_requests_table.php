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
            $table->timestamp('published_at')->nullable();
            $table->string('published_url')->nullable();
            $table->integer('reach_count')->default(0);
            $table->integer('engagement_count')->default(0);
            $table->integer('link_clicks')->default(0);
            $table->text('insight_notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content_requests', function (Blueprint $table) {
            $table->dropColumn([
                'published_at',
                'published_url',
                'reach_count',
                'engagement_count',
                'link_clicks',
                'insight_notes'
            ]);
        });
    }
};
