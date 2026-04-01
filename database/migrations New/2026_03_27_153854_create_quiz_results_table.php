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
        Schema::create('quiz_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quiz_attempt_id')
                ->constrained()
                ->cascadeOnDelete()
                ->unique(); // one result per attempt

            // FIXED HERE
            $table->foreignId('learner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->decimal('total_marks', 6, 2);
            $table->decimal('obtained_marks', 6, 2);
            $table->decimal('percentage', 5, 2);

            $table->enum('result', ['pass', 'fail']);

            $table->timestamp('published_at')->nullable();

            $table->timestamps();

            $table->index('learner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_results');
    }
};
