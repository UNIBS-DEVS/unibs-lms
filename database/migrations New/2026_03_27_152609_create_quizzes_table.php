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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();

            $table->foreignId('batch_session_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');

            $table->enum('quiz_type', ['daily', 'weekly', 'monthly', 'need based']);

            $table->unsignedTinyInteger('minimum_passing_percentage')->default(70);

            $table->unsignedSmallInteger('time_limit_minutes')->nullable();

            $table->unsignedTinyInteger('max_attempts')->default(1);

            $table->boolean('shuffle_questions')->default(false);
            $table->boolean('shuffle_options')->default(false);

            $table->boolean('show_results_immediately')->default(true);

            $table->unsignedTinyInteger('question_per_page')->default(1);

            // Visibility
            $table->date('visible_start_date')->nullable();
            $table->time('visible_start_time')->nullable();
            $table->date('visible_end_date')->nullable();
            $table->time('visible_end_time')->nullable();

            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('easy');

            $table->enum('status', ['active', 'inactive'])->default('inactive');

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
