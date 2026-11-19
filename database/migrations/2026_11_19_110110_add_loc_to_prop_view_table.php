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
        Schema::table('property_views', function (Blueprint $table) {
            $table->string('country', 64)->nullable()->after('ip_address');
            $table->string('region', 64)->nullable()->after('country');
            $table->string('city', 64)->nullable()->after('region');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('property_views', function (Blueprint $table) {
            $table->dropColumn(['country', 'region', 'city']);
        });
    }
};
