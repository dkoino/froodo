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
        Schema::table('foods', function (Blueprint $table) {
            $table->dropForeign(['user_id']); // Drops the old foreign key constraint
            $table->renameColumn('user_id', 'created_by');
            
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('foods', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
            $table->dropColumn('updated_by');
            
            $table->dropForeign(['created_by']);
            $table->renameColumn('created_by', 'user_id');
            
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
