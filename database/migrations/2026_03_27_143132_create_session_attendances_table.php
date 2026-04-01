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

            $table->foreignId('session_id')->constrained('batch_sessions')->cascadeOnDelete();

            $table->foreignId('learner_id')->constrained('users')->cascadeOnDelete();

            $table->boolean('is_present');
            $table->boolean('late_entry')->default(false);
            $table->boolean('early_exit')->default(false);

            $table->timestamp('marked_at')->useCurrent();

            $table->foreignId('marked_by')->constrained('users');

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
