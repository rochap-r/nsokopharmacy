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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            // Produits globaux sans tenant_id - partagés entre tous les tenants

            $table->string('dci');
            $table->string('dosage')->nullable();
            $table->string('forme_galenique')->nullable();


            $table->string('color_code')->nullable();

            // Les valeurs sont gérées via Enum au niveau applicatif avec Laravel 12
            $table->string('type');
            $table->string('personne')->nullable();

            // Catégorie du produit
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            // Emplacement recommandé (aisle)
            $table->foreignId('aisle_id')->nullable()->constrained()->nullOnDelete();



            $table->boolean('active')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
