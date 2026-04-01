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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained('course_topics')->cascadeOnDelete();

            $table->enum('question_type', [
                'single_choice',
                'multiple_choice',
                'text',
                'file',
            ]);

            $table->text('question_text');

            $table->decimal('max_marks', 5, 2);
            $table->decimal('negative_marks', 5, 2)->default(0);

            $table->enum('marking_type', ['automatic', 'manual']);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
