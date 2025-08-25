<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'category_id',
        'price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function stocks(): HasMany{
        return $this->hasMany(StockMovement::class);
    }
    public function balance(){
        $in = $this->stocks()->sum('quantity_in');
        $out = $this->stocks()->sum('quantity_out');
        return $in - $out;

    }
}
