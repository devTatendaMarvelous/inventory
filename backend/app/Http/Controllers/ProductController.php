<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\Core;
use App\Traits\HasApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use HasApiResponses,Core;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->checkPermission('View Products', function () {
            try {
                $products =Product::with('category')->get();
                return $this->successResponseHandler('Products', ProductResource::collection($products));
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
        return $this->checkPermission('Add Products', function () use ($request) {
            $validatedData = $this->validateRequest($request);
            if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
                return $validatedData;
            }
            $product = Product::create($validatedData);
            return  $this->createdResponseHandler('Product Added Successfully', new ProductResource($product));
        });
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        return $this->checkPermission('Edit Products', function () use ($request, $product) {

            $validatedData = $this->validateRequest($request,$product->id);
            if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
                return $validatedData;
            }
            $product->update($validatedData);
            return  $this->successResponseHandler('Product updated successfully', new ProductResource($product));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        return $this->checkPermission('Delete Products', function () use ($product) {
            $product->delete();
            return  $this->noContentResponseHandler('Product deleted successfully');
        });
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request, $id = null): \Illuminate\Http\JsonResponse|array
    {
        $rules = [
            'name' => 'required|string|max:255' . ($id ? ",$id" : ''),
            'sku' => 'required|string|max:255|unique:products,sku' . ($id ? ",$id" : ''),
            'category_id' => 'required|exists:categories,id',
            'price' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules,[
            'category_id.required' => 'Please select a category',
        ]);

        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
