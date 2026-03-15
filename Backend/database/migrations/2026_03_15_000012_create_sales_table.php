<?php

use App\Models\Sale;
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
        Schema::create(Sale::TABLE, function (Blueprint $table) {
            $table->id();
            $table->decimal('valor_total', 12, 2)->default(0);
            $table->string('cliente')->nullable();
            $table->timestamps();

            // Índices
            $table->index('cliente');
            $table->index('valor_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(Sale::TABLE);
    }
};