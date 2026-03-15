<?php

use App\Models\SaleItem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(SaleItem::TABLE, function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->unsignedInteger('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('total_preco', 12, 2);
            $table->decimal('custo_unitario', 10, 2);
            $table->decimal('lucro', 12, 2);
            $table->timestamps();

            // Índices
            $table->index(['product_id', 'sale_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(SaleItem::TABLE);
    }
};