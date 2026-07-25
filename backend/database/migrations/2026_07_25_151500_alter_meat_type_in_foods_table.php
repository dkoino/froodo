<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First add the new enum values
        DB::statement("ALTER TABLE foods MODIFY COLUMN meat_type ENUM('Schwein', 'Hähnchen', 'Pute', 'Ente', 'Rind', 'Fisch', 'Gemischt', 'Anderes', 'Kein Fleisch', 'Vegan', 'Nein', 'Unbekannt') DEFAULT 'Unbekannt'");
        
        // Update existing records
        DB::table('foods')->where('meat_type', 'Nein')->update(['meat_type' => 'Kein Fleisch']);
        
        // Remove 'Nein' from enum definition
        DB::statement("ALTER TABLE foods MODIFY COLUMN meat_type ENUM('Schwein', 'Hähnchen', 'Pute', 'Ente', 'Rind', 'Fisch', 'Gemischt', 'Anderes', 'Kein Fleisch', 'Vegan', 'Unbekannt') DEFAULT 'Unbekannt'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add 'Nein' back
        DB::statement("ALTER TABLE foods MODIFY COLUMN meat_type ENUM('Schwein', 'Hähnchen', 'Pute', 'Ente', 'Rind', 'Fisch', 'Gemischt', 'Anderes', 'Kein Fleisch', 'Vegan', 'Nein', 'Unbekannt') DEFAULT 'Unbekannt'");
        
        // Revert updates
        DB::table('foods')->whereIn('meat_type', ['Kein Fleisch', 'Vegan'])->update(['meat_type' => 'Nein']);
        
        // Remove new enum values
        DB::statement("ALTER TABLE foods MODIFY COLUMN meat_type ENUM('Schwein', 'Hähnchen', 'Pute', 'Ente', 'Rind', 'Fisch', 'Gemischt', 'Anderes', 'Nein', 'Unbekannt') DEFAULT 'Unbekannt'");
    }
};
