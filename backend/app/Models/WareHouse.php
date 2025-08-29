<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class WareHouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
    ];

    public function approvedMovements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(StockMovement::class,'warehouse_id')
            ->whereStatus('APPROVED');
    }

}
