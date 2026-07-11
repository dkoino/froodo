<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->nullable();
            $table->string('name');
            $table->string('variant')->nullable();

            $table->foreignId('main_category_id')->nullable()->constrained('main_categories')->nullOnDelete();
            $table->foreignId('sub_category_id')->nullable()->constrained('sub_categories')->nullOnDelete();
            $table->enum('meat_type', [
                'Schwein',
                'Hähnchen',
                'Pute',
                'Ente',
                'Rind',
                'Fisch',
                'Gemischt',
                'Anderes',
                'Nein',
                'Unbekannt'
            ])->default('Unbekannt');
            $table->string('state')->nullable();
            // -----------------------------------------

            $table->integer('calories_p100');
            $table->decimal('fat_p100', 8, 2);
            $table->decimal('sat_fat_p100', 8, 2)->nullable();
            $table->decimal('carbs_p100', 8, 2);
            $table->decimal('sugar_p100', 8, 2)->nullable();
            $table->decimal('fiber_p100', 8, 2)->nullable();
            $table->decimal('protein_p100', 8, 2);
            $table->decimal('salt_p100', 8, 2)->nullable();

            $table->enum('measurement_unit', ['g', 'ml']);
            $table->integer('total_amount');

            $table->string('portion_label')->nullable();
            $table->decimal('portion_amount', 8, 2)->nullable();

            $table->decimal('price', 8, 2)->nullable();
            $table->string('barcode')->nullable();

            $table->enum('source_type', ['Verpackung', 'Herstellerseite', 'Datenbank', 'Sonstiges']);
            $table->string('source_url')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
