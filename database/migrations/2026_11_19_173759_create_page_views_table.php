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
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->uuid('visitor_id')->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
            $table->string('path', 255);
            $table->string('route_name', 255)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 64)->nullable();
            $table->string('region', 64)->nullable();
            $table->string('city', 64)->nullable();
            $table->string('device_type', 16)->nullable();
            $table->string('platform', 32)->nullable();
            $table->string('browser', 32)->nullable();
            $table->timestamps();

            $table->index('created_at');
            $table->index('property_id', 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
