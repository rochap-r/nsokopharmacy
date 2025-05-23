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
        Schema::create('aisles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete(); // Nullable pour permettre des emplacements globaux
            $table->string('code'); // Par exemple : "A1", "B3", etc.
            $table->string('name')->nullable();
            $table->boolean('is_global')->default(false); // Indique si l'emplacement est global ou spécifique à un tenant
            // Optionnel : un aisle peut être lié à une catégorie pour des recommandations d'emplacement
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aisles');
    }
};
