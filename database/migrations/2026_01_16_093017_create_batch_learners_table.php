<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batch_learners', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('batch_id');
            $table->unsignedBigInteger('learner_id');

            $table->timestamps();

            // Foreign Keys
            $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
                ->cascadeOnDelete();

            $table->foreign('learner_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            // Prevent duplicate learner in same batch
            $table->unique(['batch_id', 'learner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batch_learners');
    }
};
