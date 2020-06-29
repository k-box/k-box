<?php

namespace KBox\Exceptions;

use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
        TokenMismatchException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $e
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $e)
    {
        parent::report($e);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * This transform the validation exception using the Laravel 5.4 error response style
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest('/');
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof ForbiddenException) {
            $e = new HttpException(403, $e->getMessage());
        }

        if ($e instanceof TokenMismatchException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest('login');
        }

        if ($e instanceof PostTooLargeException) {
            if ($request->expectsJson()) {
                return response()->json(['error' => trans('errors.413_text')], 413);
            }

            return trans('errors.413_text');
        }
        
        if ($e instanceof ReadonlyModeException) {
            if ($request->expectsJson()) {
                return response()->json([
                        'error' => $e->getMessage() ?? trans('errors.503-readonly_text'),
                        'wentDownAt' => $e->wentDownAt,
                        'retryAfter' => $e->retryAfter,
                        'willBeAvailableAt' => $e->willBeAvailableAt,
                    ], $e->getStatusCode());
            }
            
            return response()->make(view('errors.503-readonly', ['reason' => 'ReadonlyModeException '.$e->getMessage()]), $e->getStatusCode());
        }

        return parent::render($request, $e);
    }
}
