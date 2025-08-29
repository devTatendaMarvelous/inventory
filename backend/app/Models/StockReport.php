<?php

namespace App\Models;


use MongoDB\Laravel\Eloquent\Model;

class StockReport extends Model
{
    protected $connection = 'mongodb';
    protected $table = 'stock_reports';

    protected $fillable = [
        "warehouse",
        "warehouse_id",
        "report_time",
        "snapshot_type",
        "stock_levels"
    ];
}
