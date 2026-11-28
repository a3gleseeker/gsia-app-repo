<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('properties', 'status')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->string('status')->default('active');
            });
        }

        if (Schema::hasColumn('properties', 'is_sold')) {
            DB::table('properties')->where('is_sold', true)->update(['status' => 'sold']);

            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('is_sold');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('properties', 'status')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};