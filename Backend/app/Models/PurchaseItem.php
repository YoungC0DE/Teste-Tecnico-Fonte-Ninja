<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    public const TABLE = 'purchase_items';
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantidade',
        'preco_unitario',
        'total_custo',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}