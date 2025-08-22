<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Traits\Core;
use App\Traits\HasApiResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    use HasApiResponses,Core;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return $this->checkPermission('View Categories', function () {
            try {
                return $this->successResponseHandler('Categories', CategoryResource::collection(Category::all()));
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

        return $this->checkPermission('Add Categories', function () use ($request) {
            $validatedData = $this->validateRequest($request);

            if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
                return $validatedData;
            }

            $category = Category::create($validatedData);
            return  $this->createdResponseHandler('Category Added Successfully', new CategoryResource($category));
        });
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        return $this->checkPermission('Edit Categories', function () use ($request, $category) {

            $validatedData = $this->validateRequest($request,$category->id);
            if ($validatedData instanceof \Illuminate\Http\JsonResponse) {
                return $validatedData;
            }
            $category->update($validatedData);
            return  $this->successResponseHandler('Category updated successfully', new CategoryResource($category));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        return $this->checkPermission('Delete Categories', function () use ($category) {
           $category->delete();
            return  $this->noContentResponseHandler('Category deleted successfully');
        });
    }

    /**
     * Validate request data.
     */
    private function validateRequest(Request $request, $id = null): \Illuminate\Http\JsonResponse|array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:categories,name' . ($id ? ",$id" : ''),
            'description' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->errorValidationResponseHandler('Validation failed', $validator->errors()->toArray());
        }

        return $validator->validated();
    }
}
