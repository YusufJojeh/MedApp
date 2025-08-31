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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing columns that we don't need
            $table->dropColumn(['name', 'email_verified_at', 'remember_token']);

            // Add new columns for medical booking system
            $table->string('username', 50)->unique()->after('id');
            $table->string('email', 100)->change();
            $table->enum('role', ['admin', 'doctor', 'patient'])->default('patient')->after('password');
            $table->string('first_name', 50)->after('role');
            $table->string('last_name', 50)->after('first_name');
            $table->string('phone', 20)->nullable()->after('last_name');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('phone');
            $table->text('profile_image')->default('')->after('status');
            $table->timestamp('last_login')->nullable()->after('profile_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'role', 'first_name', 'last_name',
                'phone', 'status', 'profile_image', 'last_login'
            ]);

            $table->string('name')->after('id');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->rememberToken()->after('password');
        });
    }
};
