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
        Schema::create('clients_master', function (Blueprint $table) {
            $table->id();
            $table->string('client_code', 50)->unique();
            $table->string('client_name', 150);
            $table->text('client_ship_to_address')->nullable();
            $table->text('client_bill_to_address')->nullable();
            $table->string('client_gst', 50)->nullable();
            $table->string('client_pan', 50)->nullable();
            $table->string('client_spoc_name', 100)->nullable();
            $table->string('client_spoc_email', 100)->nullable();
            $table->string('client_spoc_mobile', 20)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('logo_path', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_master');
    }
};
