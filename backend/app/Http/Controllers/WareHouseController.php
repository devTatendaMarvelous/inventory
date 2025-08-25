<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWareHouseRequest;
use App\Http\Requests\UpdateWareHouseRequest;
use App\Http\Resources\WareHouseResource;
use App\Models\WareHouse;
use App\Traits\Core;
use App\Traits\HasApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WareHouseController extends Controller
{
    use HasApiResponses, Core;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->checkPermission('View Warehouses', function () {
            try {
                return $this->successResponseHandler('Warehouses', WareHouseResource::collection(WareHouse::all()));
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

        return $this->checkPermission('Add Warehouses', function () use ($request) {
            $validatedData = $this->validateRequest($request);

            if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
                return $validatedData;
            }

            $warehouse = WareHouse::create($validatedData);
            return $this->createdResponseHandler('WareHouse Added Successfully', new WareHouseResource($warehouse));
        });
    }

    public function show(WareHouse $warehouse)
    {
        return $this->checkPermission('View Warehouses', function () use ($warehouse) {
            return $this->successResponseHandler('Warehouse', new WareHouseResource($warehouse));
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WareHouse $warehouse)
    {
        return $this->checkPermission('Edit Warehouses', function () use ($request, $warehouse) {

            $validatedData = $this->validateRequest($request, $warehouse->id);
            if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
                return $validatedData;
            }
            $warehouse->update($validatedData);
            return $this->successResponseHandler('WareHouse updated successfully', new WareHouseResource($warehouse));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WareHouse $warehouse)
    {
        return $this->checkPermission('Delete Warehouses', function () use ($warehouse) {
            $warehouse->delete();
            return $this->noContentResponseHandler('WareHouse deleted successfully');
        });
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request, $id = null): \Illuminate\Http\JsonResponse|array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:ware_houses,name' . ($id ? ",$id" : ''),
            'location' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
