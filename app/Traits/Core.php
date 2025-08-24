<?php

namespace App\Traits;

use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;

trait Core
{
    public function respondWithToken($token): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'user' => new UserResource( auth()->user()),
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'roles' => auth()->user()->roles()->pluck('name'),
            'permissions' => auth()->user()->getAllPermissions()->pluck('name'),

        ]);
    }

    public function errorOccurredMessage(): string
    {
        return 'An error occurred while processing your request. Please try again later.';
    }
    public function notAllowedMessage(): string
    {
        return 'You are not allowed to access this page';
    }
    /**
     * Check user permission and execute callback if authorized.
     */
    private function checkPermission($ability, callable $callback)
    {

        if (!Gate::allows($ability)) {
            return $this->forbiddenResponseHandler($this->notAllowedMessage());
        }

        try {
            return $callback();
        } catch (\Exception $exception) {
            dd($exception->getMessage());
            return $this->errorResponseHandler($this->errorOccurredMessage());
        }
    }
    public function systemPermissions(): array
    {
        return [
            //Roles Permissions
            'Access Roles',
            'View Roles',
            'View Permissions',
            'Edit Roles',
            'Delete Roles',
            'Add Roles',
            'Manage Roles',

            //Categories Permissions
            'Access Categories',
            'View Categories',
            'Edit Categories',
            'Delete Categories',
            'Add Categories',

            //Products Permissions
            'Access Products',
            'View Products',
            'Edit Products',
            'Delete Products',
            'Add Products',

            //Warehouses Permissions
            'Access Warehouses',
            'View Warehouses',
            'Edit Warehouses',
            'Delete Warehouses',
            'Add Warehouses',

            //Stocks Permissions
            'Access Stocks',
            'View Stocks',
            'Edit Stocks',
            'Delete Stocks',
            'Add Stocks',



        ];
    }
}
