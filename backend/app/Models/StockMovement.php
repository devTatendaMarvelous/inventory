<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id' ,
        'warehouse_id' ,
        'source_id' ,
        'movement_type',
        'quantity_in' ,
        'quantity_out',
        'unit_price',
        'status',
        'notes',
        'initiated_by'
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class);
    }
    public function source(): BelongsTo
    {
        return $this->belongsTo(WareHouse::class);
    }
}
