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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // appointment, payment, system, etc.
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable(); // icon class or emoji
            $table->string('color')->default('blue'); // blue, green, red, yellow, gray
            $table->json('data')->nullable(); // additional data
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->enum('channel', ['database', 'email', 'sms', 'push'])->default('database');
            $table->boolean('is_sent')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['type', 'created_at']);
            $table->index(['priority', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
