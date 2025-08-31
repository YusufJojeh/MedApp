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
        Schema::table('login_activities', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('status');
        });

        Schema::table('login_activities', function (Blueprint $table) {
            // Recreate the enum column with the new values
            $table->enum('status', ['success', 'failed', 'blocked', 'logout'])->default('success')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            // Drop the updated enum column
            $table->dropColumn('status');
        });

        Schema::table('login_activities', function (Blueprint $table) {
            // Recreate the original enum column
            $table->enum('status', ['success', 'failed', 'blocked'])->default('success')->after('user_id');
        });
    }
};
