<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public const TABLE = 'purchases';

    protected $fillable = [
        'valor_total',
        'fornecedor',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}