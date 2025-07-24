<?php 
// src/database/migrations/xxxx_xx_xx_create_route_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('route_logs_mapping', function (Blueprint $table) {
            $table->id();
            $table->string('action')->nullable();
            $table->string('uri');
            $table->string('name')->nullable();
            $table->string('case_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('route_logs_mapping');
    }
};
