<?php

namespace App\Console\Commands;

use App\Traits\Core;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Jobs\NotifyLowStockJob;
use App\Models\Product;
use App\Models\WareHouse;
use App\Models\StockMovement;

class CheckLowStock extends Command
{
    use Core;

    protected $signature = 'stock:check-low';
    protected $description = 'Check stock levels and notify if below threshold';

    public function handle()
    {
        $threshold = 10000000;//env('LOW_STOCK_THRESHOLD',10);

        $warehouses = WareHouse::all();

        foreach ($warehouses as $warehouse) {
            $products = Product::all();

            foreach ($products as $product) {

//                if ($this->isLocked($warehouse->id, $product->id)) {
//                    continue;
//                }

                $totals = StockMovement::where('warehouse_id', $warehouse->id)
                    ->where('product_id', $product->id)
                    ->selectRaw('SUM(quantity_in) as quantity_in, SUM(quantity_out) as quantity_out')
                    ->first();

                $quantityIn = $totals->quantity_in ?? 0;
                $quantityOut = $totals->quantity_out ?? 0;
                $balance = $quantityIn - $quantityOut;

                if ($balance <= $threshold) {
                    // publish to Redis
                    Redis::publish('low_stock_channel', json_encode([
                        'warehouse' => $warehouse->name,
                        'product' => $product->name,
                        'balance' => $balance,
                        'threshold' => $threshold,
                        'time' => now()->toDateTimeString()
                    ]));

                    // Dispatch notification job
                    NotifyLowStockJob::dispatch($warehouse, $product, $balance, $threshold);
                    $this->lockProduct($warehouse->id, $product->id);
                }
            }
        }

        $this->info("Stock check completed at " . now());
    }
}
