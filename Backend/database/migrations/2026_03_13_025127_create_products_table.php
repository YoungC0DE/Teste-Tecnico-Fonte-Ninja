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
            $table->string('name'); // nome do produto
            $table->string('sku')->unique(); // identificador
            $table->unsignedInteger('stock')->default(0); // quantidade atual
            $table->decimal('average_cost', 10, 2)->default(0); // custo médio do produto
            $table->timestamps();
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
