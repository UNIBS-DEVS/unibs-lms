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
        Schema::create('batch_tocs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('batches')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->nullable() // ✅ REQUIRED
                ->constrained('courses')
                ->nullOnDelete();

            $table->foreignId('trainer_id')
                ->nullable() // ✅ REQUIRED
                ->constrained('users')
                ->nullOnDelete();

            $table->string('title');

            $table->date('planned_start_date');
            $table->date('planned_end_date');

            $table->date('actual_start_date')->nullable();
            $table->date('actual_end_date')->nullable();

            $table->text('remark_admin')->nullable();
            $table->text('remark_trainer')->nullable();

            $table->enum('status', [
                'planned',
                'in_progress',
                'on_hold',
                'completed'
            ])->default('planned');

            $table->unsignedTinyInteger('percentage')
                ->default(0)
                ->comment('0–100');

            $table->foreignId('created_by')
                ->nullable() // ✅ REQUIRED
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable() // ✅ REQUIRED
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->index(['batch_id', 'course_id']);
            $table->index('trainer_id');
            $table->index('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_tocs');
    }
};
