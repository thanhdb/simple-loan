<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($request->wantsJson()) {   //Add 'Accept: application/json' in request header
            return $this->handleApiException($request, $e);
        }

        return parent::render($request, $e);
    }


    /**
     * Handle API exception
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     *
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleApiException($request, $exception)
    {
        switch ($exception) {
            case $exception instanceof AuthenticationException:
                $message = $exception->getMessage();
                $statusCode = Response::HTTP_FORBIDDEN;
                break;

            case $exception instanceof AuthorizationException:
                $message = $exception->getMessage();
                $statusCode = Response::HTTP_FORBIDDEN;
                break;

            case $exception instanceof NotFoundHttpException:
                $message = $exception->getMessage() ?? "Resource not found";
                $statusCode = Response::HTTP_NOT_FOUND;
                break;

            case $exception instanceof BadRequestHttpException:
                $message = $exception->getMessage();
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;

            case $exception instanceof ServiceUnavailableHttpException:
                $message = $exception->getMessage();
                $statusCode = Response::HTTP_SERVICE_UNAVAILABLE;
                break;

            case $exception instanceof ValidationException:
                $message = 'Invalid data provided. Please try again.';
                $errors = $exception->errors();
                foreach ($errors as $key => $value) {
                    $message = $value[0];
                    break;
                }
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;

            case $exception instanceof HttpResponseException:
                $message = $exception->getMessage();
                $statusCode = $exception->getStatusCode();
                break;

            default:
                $message = 'Something went wrong, Please try again later.';
                if (method_exists($exception, 'getStatusCode')) {
                    $statusCode = $exception->getStatusCode();
                } else {
                    $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
                }
                break;
        }

        return $this->errorResponse($message, $statusCode);
    }

}
