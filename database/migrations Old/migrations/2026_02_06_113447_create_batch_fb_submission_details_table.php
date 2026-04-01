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
        Schema::create('batch_fb_submission_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('summary_id')
                ->constrained('batch_fb_summaries')
                ->cascadeOnDelete();

            $table->string('category')->nullable();

            $table->text('question');
            $table->unsignedTinyInteger('score'); // 1–5

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_fb_submission_details');
    }
};
