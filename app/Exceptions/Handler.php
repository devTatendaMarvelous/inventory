<?php

namespace App\Exceptions;

use App\Traits\HasApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\Concerns\Has;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HasApiResponses;
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response|\Illuminate\Http\RedirectResponse
    {
        if ($e instanceof ModelNotFoundException) {
            $modelClass = $e->getModel();
            $modelName = $modelClass ? Str::headline(class_basename($modelClass)) : 'Record';
            return $this->notFoundResponseHandler("The requested {$modelName} does not exist.");
        }
        if ($e instanceof NotFoundHttpException) {
            return $this->notFoundResponseHandler("The requested URL was not found on this server.");
        }
        return parent::render($request, $e);
    }

}
