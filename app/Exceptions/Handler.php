<?php

namespace KBox\Exceptions;

use Exception;
use ErrorException;
use Illuminate\Http\JsonResponse;
use GuzzleHttp\Exception\TransferException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Klink\DmsAdapter\Exceptions\KlinkException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Debug\Exception\FatalErrorException;
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
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
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
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
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

        // if($e instanceof HttpResponseException)
        // {
        return parent::render($request, $e);
        // }

        // if(app()->environment('local')){
        
        // 	if ($this->isHttpException($e))
        // 	{
        // 		return $this->renderHttpException($e);
        // 	}
        // 	else if($e instanceof TokenMismatchException)
        //     {
        //     	if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.token_mismatch_exception')), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.token_mismatch_exception'), 500);
        //     	}
    
        //         return response(trans('errors.token_mismatch_exception'), 500);
        //     }
        // 	else if($e instanceof FatalErrorException){
        // 		if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.fatal', array('reason' => $e->getMessage()))), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.fatal', array('reason' => $e->getMessage())), 500);
        //     	}
    
        //         return response(trans('errors.fatal', array('reason' => $e->getMessage())), 500);
        // 	}
            
        // 	return parent::render($request, $e);
            
        // }
        // else {
        // 	\Log::error('Exception Handler render', ['e' => $e]);

        // 	if ($this->isHttpException($e) )
        // 	{
                
        // 		if($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
                    
        // 			$message = empty($e->getMessage()) ? trans('errors.404_text') : $e->getMessage();
                    
        // 			if($request->wantsJson()){
        //         		return new JsonResponse(array('error' => $message), 404);
        //         	}
        // 			else if($request->ajax()){
        //         		return response($message, 404);
        //         	}
                    
        // 			return response()->make(view('errors.404', ['error_message' => $message, 'reason' => $message]), 404);
        // 		}
        // 		else if($e->getStatusCode(413)){
        // 			if($request->wantsJson()){
        //         		return new JsonResponse(array('error' => trans('errors.413_text')), 413);
        //         	}
        // 			else if($request->ajax()){
        //         		return response(trans('errors.413_text'), 413);
        //         	}
        // 		}
                
        // 		return parent::render($request, $e);
        // 	}
        // 	else if($e instanceof ModelNotFoundException)
        //     {
        //     	if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.404_text')), 404);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.404_text'), 404);
        //     	}
    
        //         return response()->make(view('errors.404', ['reason' => 'ModelNotFoundException ' . $e->getMessage()]), 404);
        //     }
        // 	else if($e instanceof TokenMismatchException)
        //     {
        //     	if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.token_mismatch_exception')), 401);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.token_mismatch_exception'), 401);
        //     	}
    
        //         return response(trans('errors.token_mismatch_exception'), 401);
        //     }
        // 	else if($e instanceof ForbiddenException)
        //     {
        //     	if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.403_text')), 403);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.403_text'), 403);
        //     	}
    
        //         return response()->make(view('errors.403', ['reason' => 'ForbiddenException ' . $e->getMessage()]), 403);
        //     }
        // 	else if($e instanceof KlinkException)
        //     {
        //     	if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.klink_exception_text')), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.klink_exception_text'), 500);
        //     	}
    
        //         return response(trans('errors.klink_exception_text'), 500);
        //     }
        // 	else if($e instanceof FatalErrorException){
        // 		if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.fatal', array('reason' => $e->getMessage()))), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.fatal', array('reason' => $e->getMessage())), 500);
        //     	}
    
        //         return response()->make(view('errors.fatal', ['reason' => 'FatalErrorException ' . $e->getMessage()]), 500);
        // 	}
        //     else if($e instanceof ErrorException){
        // 		if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.fatal', array('reason' => $e->getMessage()))), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.fatal', array('reason' => $e->getMessage())), 500);
        //     	}
    
        //         return response()->make(view('errors.500', ['reason' => 'ErrorException ' . $e->getMessage()]), 500);
        // 	}
        // 	else if($e instanceof TransferException){
        // 		if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.kcore_connection_problem', array('reason' => $e->getMessage()))), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.kcore_connection_problem', array('reason' => $e->getMessage())), 500);
        //     	}
    
        //         return response()->make(view('errors.kcore_connection', ['reason' => 'TransferException ' . $e->getMessage()]), 500);
        // 	}
        // 	else
        // 	{
        //         if($request->wantsJson()){
        //     		return new JsonResponse(array('error' => trans('errors.fatal', array('reason' => $e->getMessage()))), 500);
        //     	}
        // 		else if($request->ajax()){
        //     		return response(trans('errors.fatal', array('reason' => $e->getMessage())), 500);
        //     	}
    
        //         return response()->make(view('errors.500', ['reason' => $e->getMessage()]), 500);
        // 	}
    
        // }
    }
}
