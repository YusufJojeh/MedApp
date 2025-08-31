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
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('user_message');
            $table->text('ai_response');
            $table->string('intent')->nullable();
            $table->json('metadata')->nullable(); // Store additional data like confidence, context, etc.
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('intent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
