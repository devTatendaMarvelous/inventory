<?php

namespace App\Http\Controllers;

use App\Models\StockReport;
use App\Traits\HasApiResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    use HasApiResponses;
    /**
     * Display a listing of the resource.
     */
//    public function index(Request $request)
//    {
//        $levels =StockReport::when($request->filled('start_date'), function ($query) use ($request) {
//            $query->whereDate('created_at', '>=', Carbon::parse( $request->start_date)->startOfDay());
//        })->when($request->filled('end_date'), function ($query) use ($request) {
//            $query->whereDate('created_at', '<=', Carbon::parse( $request->end_date)->endOfDay());
//        })->when($request->filled('product_id'), function ($query) use ($request) {
//            $query->where('product_id', $request->product_id);
//        })->when($request->filled('warehouse_id'), function ($query) use ($request) {
//            $query->where('warehouse_id', $request->warehouse_id);
//        })->get();
//        return $this->successResponseHandler("Stock Levels" ,$levels);
//           }


    public function index(Request $request)
    {
        $query = StockReport::query();

        if ($request->filled('start_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $start);
        }
        if ($request->filled('end_date')) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $end);
        }
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', (int) $request->warehouse_id);
        }

        $levels = $query->get();

        return $this->successResponseHandler("Stock Levels", $levels);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
