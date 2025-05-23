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
        Schema::create('tenant_inventories', function (Blueprint $table) {
            $table->id();
            // Identifiant du tenant
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();

            // Association avec un produit du catalogue
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();

            // Détails spécifiques au tenant
            $table->decimal('purchase_price', 10, 2)->nullable(); // Prix d'achat
            $table->decimal('selling_price', 10, 2); // Prix de vente
            $table->unsignedInteger('stock_quantity')->default(0);
            $table->unsignedInteger('alert_threshold')->default(5); // Seuil d'alerte pour réapprovisionnement

            // Emplacement personnalisé dans la pharmacie de ce tenant
            $table->foreignId('aisle_id')->nullable()->constrained()->nullOnDelete();

            // Dates importantes
            $table->date('expiry_date')->nullable();
            $table->date('last_restock_date')->nullable();

            // Autres informations spécifiques au tenant
            $table->boolean('available_for_sale')->default(true);

            $table->timestamps();
            
            // Index composé pour optimiser les requêtes fréquentes
            $table->unique(['tenant_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_inventories');
    }
};
