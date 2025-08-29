<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\WareHouse;
use Illuminate\Console\Command;
use App\Models\StockMovement;
use App\Models\StockReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateStockReport extends Command
{
    protected $signature = 'report:stock';
    protected $description = 'Generate daily stock snapshot';

    public function handle()
    {

        $warehouses = Warehouse::with('approvedMovements','approvedMovements.product')->get();
        foreach ($warehouses as $warehouse) {


            $grouped = $warehouse->approvedMovements->groupBy('product_id');
            $stockLevels = $grouped->map(function($rows, $productId) {
                $quantityIn = $rows->sum('quantity_in');
                $quantityOut = $rows->sum('quantity_out');
                $balance = $quantityIn - $quantityOut;
                return [
                    "product" => Product::find($productId)->name??'Product',
                    "quantity_in" => $quantityIn,
                    "quantity_out" => $quantityOut,
                    "balance" => $balance,
                ];
            })->values();

            $snapshotType = (Carbon::now()->hour < 12) ? 'MORNING' : 'EVENING';

            StockReport::create([
                "warehouse" => $warehouse->name,
                "warehouse_id" => $warehouse->id,
                "report_time" => now(),
                "snapshot_type" => $snapshotType, // e.g. morning/evening
                "stock_levels" => $stockLevels->toArray()
            ]);
        }

        $this->info("Stock snapshot ({$snapshotType}) saved successfully to MongoDB!");
    }
}
