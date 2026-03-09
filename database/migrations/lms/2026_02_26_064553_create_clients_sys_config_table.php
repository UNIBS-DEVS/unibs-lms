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
        Schema::create('clients_sys_config', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->unique();

            $table->foreign('client_id')
                ->references('id')
                ->on('clients_master')
                ->onDelete('cascade');

            $table->string('db_host');
            $table->string('db_name');
            $table->string('db_username');
            $table->string('db_password');
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_admin_user')->nullable();
            $table->string('smtp_admin_pass')->nullable();
            $table->enum('smtp_auth', ['dns', 'ssl']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients_sys_config');
    }
};
