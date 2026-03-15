<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const TABLE = 'products';

    protected $fillable = [
        "nome",
        "estoque",
        "custo_medio",
        "preco_venda",
    ];
}
