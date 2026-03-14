<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    public const TABLE = 'sales';

    protected $fillable = [
        'total_value',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}