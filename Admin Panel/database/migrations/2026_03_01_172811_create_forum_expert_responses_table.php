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
        Schema::create('forum_expert_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_reply_id')->constrained('forum_replies')->cascadeOnDelete();
            $table->foreignId('expert_id')->constrained('experts')->cascadeOnDelete();
            $table->boolean('is_expert_advice')->default(true);
            $table->text('recommendation')->nullable();
            $table->unsignedInteger('helpful_votes')->default(0);
            $table->timestamps();

            $table->unique('forum_reply_id'); // one response per reply
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_expert_responses');
    }
};
