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
        Schema::create('portions', function (Blueprint $table) {
            $table->id();
            // Link to Food-Table (cascadeOnDelete: If a food is deleted, all associated portions will be deleted as well)
            $table->foreignId('food_id')->constrained('foods')->cascadeOnDelete();
            
            $table->string('name');
            $table->integer('amount');
            $table->string('unit');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portions');
    }
};
