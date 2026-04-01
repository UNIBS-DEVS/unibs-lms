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
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->foreignId('trainer_id') 
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title');

            $table->date('plan_start_date');
            $table->date('plan_end_date');

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
                ->comment('Progress percentage (0–100)');



            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_toc');
    }
};
