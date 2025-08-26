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
            if($stockMovement->status!='PENDING'){
                return $this->unprocessableContentResponseHandler('Stock Movement is  already validated');
            }elseif($request->status!='PENDING'){
                $validated['validated_by']=auth()->id();
            }

            $stockMovement->product_id    = $validated['product_id'];
            $stockMovement->warehouse_id  = $validated['warehouse_id'];
            $stockMovement->movement_type = $validated['movement_type'];
            $stockMovement->quantity_in      = $validated['quantity_in'];
            $stockMovement->unit_price         = $validated['unit_price'];
            $stockMovement->status        = $validated['status'];
            $stockMovement->notes         = $validated['notes'] ?? null;
            $stockMovement->initiated_by  = $validated['initiated_by'];
            $stockMovement->validated_by  = $validated['validated_by'];
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
            'initiated_by'=>'nullable|exists:users,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
