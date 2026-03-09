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
        Schema::create('session_attendances', function (Blueprint $table) {
            $table->id();

            // Session
            $table->foreignId('session_id')
                ->constrained('batch_sessions')
                ->cascadeOnDelete();

            // Learner
            $table->foreignId('learner_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Attendance status
            $table->enum('present', ['present', 'absent']);

            $table->enum('late_entry', ['yes', 'no'])
                ->default('no');

            $table->enum('early_exit', ['yes', 'no'])
                ->default('no');

            // Marking info
            $table->dateTime('marked_at');

            $table->foreignId('marked_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->enum('source', ['trainer', 'auto'])
                ->default('trainer');

            $table->text('remarks')->nullable();

            $table->timestamps();

            // Prevent duplicate attendance for same learner in same session
            $table->unique(['session_id', 'learner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_attendances');
    }
};
