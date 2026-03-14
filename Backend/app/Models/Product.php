<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public const TABLE = 'products';

    protected $fillable = [
        "name",
        "sku",
        "stock",
        "average_cost",
    ];
}
