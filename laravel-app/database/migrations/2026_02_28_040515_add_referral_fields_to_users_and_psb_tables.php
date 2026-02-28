<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add referral_code to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 20)->nullable()->unique()->after('remember_token');
        });

        // Generate referral codes for existing users
        $users = DB::table('users')->whereNull('referral_code')->get();
        foreach ($users as $user) {
            $code = strtoupper(Str::random(8));
            // Ensure uniqueness
            while (DB::table('users')->where('referral_code', $code)->exists()) {
                $code = strtoupper(Str::random(8));
            }
            DB::table('users')->where('id', $user->id)->update(['referral_code' => $code]);
        }

        // 2. Add referred_by to psb_registrations table
        Schema::table('psb_registrations', function (Blueprint $table) {
            $table->foreignId('referred_by')->nullable()->after('berkas_ijazah')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psb_registrations', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropColumn('referred_by');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['referral_code']);
            $table->dropColumn('referral_code');
        });
    }
};
