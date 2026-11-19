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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name', 256);
            $table->string('link', 512)->nullable();
            $table->string('type', 24);
            $table->decimal('size', 10, 2)->default(0.00);
            $table->decimal('price', 10, 2)->default(0.00);
            $table->boolean('is_sold')->default(false);
            $table->string('postal', 16);
            $table->string('city', 64);
            $table->string('description', 512);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
