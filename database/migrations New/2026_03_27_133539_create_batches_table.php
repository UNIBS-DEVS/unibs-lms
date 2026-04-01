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
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); 

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->date('start_date');
            $table->date('end_date')->nullable();

            // Percentages
            $table->unsignedInteger('attendance_percentage')->default(20);
            $table->unsignedInteger('quiz_percentage')->default(70);
            $table->unsignedInteger('feedback_percentage')->default(10);

            $table->unsignedInteger('red_percentage')->default(60);
            $table->unsignedInteger('amber_percentage')->default(80);
            $table->unsignedInteger('green_percentage')->default(90);

            // Values
            $table->unsignedInteger('present_value')->default(5);
            $table->decimal('late_entry_value', 5, 2)->default(1);
            $table->decimal('early_exit_value', 5, 2)->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
