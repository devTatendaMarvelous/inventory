<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStockMovementRequest;
use App\Http\Requests\UpdateStockMovementRequest;
use App\Http\Resources\StockMovementResource;
use App\Models\StockMovement;
use App\Traits\Core;
use App\Traits\HasApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StockMovementController extends Controller
{
    use HasApiResponses,Core;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->checkPermission('View Stocks', function () {
            try {
                $stocks =StockMovement::with('product','warehouse','source')->get();

                return $this->successResponseHandler('Stocks', StockMovementResource::collection($stocks));
            } catch (\Exception $exception) {
                return $this->errorResponseHandler($this->errorOccurredMessage());
            }
        });
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->checkPermission('Add Stocks', function () use ($request) {
            $validated = $this->validateRequest($request);
            if ($validated instanceof \Illuminate\Http\JsonResponse) {
                return $validated;
            }
            $validated['initiated_by'] = auth()->id();
            $stockMovement = StockMovement::create($validated);
            return  $this->createdResponseHandler('StockMovement Added Successfully', new StockMovementResource($stockMovement));
        });
    }
public function show(StockMovement $stockMovement)
{
    return $this->checkPermission('View Stocks', function () use ($stockMovement) {

        $stockMovement->load('product', 'warehouse', 'source');
        return $this->successResponseHandler('StockMovement', new StockMovementResource($stockMovement));
    });
}


public function validation(Request $request, $id)
{
    $stockMovement = StockMovement::find($id);
    if (!$stockMovement) {
        return $this->notFoundResponseHandler('Stock Movement not found');
    }
    return $this->checkPermission('Validate Stocks', function () use ($stockMovement, $request) {
        if ($stockMovement->status !== 'PENDING') {
            return $this->unprocessableContentResponseHandler('Stock Movement is already validated');
        }
        $validated = Validator::make($request->all(), [
            'status' => 'required|in:APPROVED,REJECTED',
            'notes' => 'nullable|string',
        ]);
        if ($validated->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validated->errors()->toArray());
        }
        $stockMovement->status = $request->status;
        $stockMovement->notes = $request->notes ?? null;
        $stockMovement->save();
        return $this->successResponseHandler('StockMovement validated successfully', new StockMovementResource($stockMovement));
    });
}



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        return $this->checkPermission('Edit Stocks', function () use ($request, $id) {

            $validated = $this->validateRequest($request,$id);
            $stockMovement=StockMovement::find($id);

            if ($validated instanceof \Illuminate\Http\JsonResponse) {
                return $validated;
            }

            $stockMovement->product_id    = $validated['product_id'];
            $stockMovement->warehouse_id  = $validated['warehouse_id'];
            $stockMovement->movement_type = $validated['movement_type'];
            $stockMovement->quantity_in      = $validated['quantity_in'];
            $stockMovement->quantity_in      = $validated['quantity_in'];
            $stockMovement->quantity_out      = $validated['quantity_out'];
            $stockMovement->unit_price         = $validated['unit_price'];
            $stockMovement->notes         = $validated['notes'] ?? null;
            $stockMovement->save();
            $stockMovement->load('product','warehouse','source');

            return  $this->successResponseHandler('StockMovement updated successfully', new StockMovementResource($stockMovement));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StockMovement $stockMovement)
    {
        return $this->checkPermission('Delete Stocks', function () use ($stockMovement) {
            $stockMovement->delete();
            return  $this->noContentResponseHandler('StockMovement deleted successfully');
        });
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request, $id = null): \Illuminate\Http\JsonResponse|array
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:ware_houses,id',
            'source_id' => 'nullable|exists:ware_houses,id',
            'movement_type' =>'required|in:IN,OUT,TRANSFER',
            'quantity_in' => 'nullable|numeric',
            'quantity_out' => 'nullable|numeric',
            'unit_price' => 'required||numeric',
            'status'=>'nullable|in:APPROVED,REJECTED',
            'notes'=>'nullable',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
