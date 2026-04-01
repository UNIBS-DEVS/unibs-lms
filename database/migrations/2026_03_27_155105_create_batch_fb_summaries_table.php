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
        Schema::create('batch_fb_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            // Who submitted feedback
            $table->foreignId('submitted_by')->constrained('users')->cascadeOnDelete();

            // Optional: trainer being reviewed
            $table->foreignId('trainer_id')->nullable()->constrained('users')->cascadeOnDelete();

            $table->enum('type', ['trainer', 'learner']);

            $table->decimal('avg_score', 5, 2)->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index(['batch_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_fb_summaries');
    }
};
