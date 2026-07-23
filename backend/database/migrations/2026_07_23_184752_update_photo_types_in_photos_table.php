<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Add new values 'packaging', 'content', and 'other' to enum to prevent Data truncated errors
        DB::statement("ALTER TABLE photos MODIFY COLUMN type ENUM('front', 'product', 'nutrition', 'ingredients', 'barcode', 'general', 'packaging', 'content', 'other') DEFAULT 'general'");

        // Step 2: Map old values to the new categories
        DB::table('photos')->where('type', 'front')->update(['type' => 'packaging']);
        DB::table('photos')->where('type', 'product')->update(['type' => 'packaging']);
        DB::table('photos')->where('type', 'general')->update(['type' => 'other']);

        // Step 3: Remove old 'front', 'product', and 'general' values from the enum definition
        DB::statement("ALTER TABLE photos MODIFY COLUMN type ENUM('packaging', 'content', 'nutrition', 'ingredients', 'barcode', 'other') DEFAULT 'other'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            //
        });
    }
};
