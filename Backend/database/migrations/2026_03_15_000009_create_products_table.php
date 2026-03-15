<?php

use App\Models\Product;
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
        Schema::create(Product::TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // nome do produto
            $table->unsignedInteger('estoque')->default(0); // quantidade atual
            $table->decimal('custo_medio', 10, 2)->default(0); // custo médio do produto
            $table->decimal('preco_venda', 10, 2)->default(0); // preço de venda sugerido
            $table->timestamps();

            // Índices
            $table->index('nome');
            $table->index('preco_venda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Product::TABLE);
    }
};