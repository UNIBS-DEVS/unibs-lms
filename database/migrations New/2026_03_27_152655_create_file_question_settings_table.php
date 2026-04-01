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
        Schema::create('file_question_settings', function (Blueprint $table) {
            $table->id();

            // Link to the question
            $table->unsignedBigInteger('question_id')->unique();

            // Allowed file types (csv, pdf, jpg, etc.)
            $table->string('allowed_file_types')->nullable();

            // Maximum file size in MB
            $table->integer('max_file_size_mb')->default(5);

            // Optional storage path override
            $table->string('upload_path')->nullable();

            $table->timestamps();

            // Foreign key
            $table->foreign('question_id')
                ->references('id')->on('questions')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_question_settings');
    }
};
