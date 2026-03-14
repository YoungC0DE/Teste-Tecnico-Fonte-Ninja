<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    public const TABLE = 'purchases';

    protected $fillable = [
        'total_value',
    ];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}