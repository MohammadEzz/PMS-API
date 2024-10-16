<?php

namespace App\Exceptions;

use App\Http\Helpers\Api\ApiMessagesTemplate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {

        $this->renderable(function(QueryException $e) {
            return ApiMessagesTemplate::createResponse(false, 503, $e->getMessage());
        });

        $this->renderable(function(NotFoundHttpException $e) {
            return ApiMessagesTemplate::createResponse(false, 404, $e->getMessage());
        });

        $this->renderable(function(ValidationException $e){
            return ApiMessagesTemplate::createResponse(false, 422, $e->getMessage());
        });

    }
}
