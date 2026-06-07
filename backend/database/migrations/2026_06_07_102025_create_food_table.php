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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            // Link to Brand-Table 
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete(); 
            
            $table->string('name');
            $table->string('variant')->nullable();
            $table->string('barcode')->nullable();
            
            // Nutritional Values per 100g/ml
            $table->integer('calories_p100');
            $table->decimal('fat_p100', 8, 2);
            $table->decimal('sat_fat_p100', 8, 2);
            $table->decimal('carbs_p100', 8, 2);
            $table->decimal('sugar_p100', 8, 2);
            $table->decimal('fiber_p100', 8, 2);
            $table->decimal('protein_p100', 8, 2);
            $table->decimal('salt_p100', 8, 2);
            
            // Meta-Data
            $table->string('base_unit');
            $table->integer('quantity_total');
            $table->decimal('price', 8, 2)->nullable();
            $table->string('source');
            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
