<?php

namespace App\Exceptions;

use Closure;
use Throwable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Laravel\Passport\Exceptions\MissingScopeException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
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

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof MissingScopeException) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated',
            ], 401);
        }
        // if ($exception instanceof \Illuminate\View\ViewException) {
        //     // Handle the ViewException
        //     return response()->view('error.500', [], 500);
        // }

        if ($this->isHttpException($exception)) {
            if ($exception->getStatusCode() == 404) {
                return response()->view('error.404', [], 404); // Ensure the correct view path
            }
            if ($exception->getStatusCode() == 500) {
                return response()->view('error.500', [], 500); // Ensure the correct view path
            }
            if ($exception->getStatusCode() == 422) {
                return response()->view('error.500', [], 422); // Ensure the correct view path
            }
        }

        if ($exception instanceof HttpResponseException) {
            $this->prepareException($exception);
            return $exception->getResponse();
        }

        if ($exception instanceof AuthenticationException) {
            return $this->unauthenticated($request, $exception);
        }

        if ($exception instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($exception, $request);
        }

        return $this->prepareResponse($request, $exception);
    }


    public function unauthenticated($request, AuthenticationException $exception)
    {

        if (str_contains($request->url(), 'api/v1/user')) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated',
            ], 401);
        } elseif (str_contains($request->url(), 'api/v1/admin')) {
            return response()->json([
                'status' => 401,
                'message' => 'Unauthenticated',
            ], 401);
        } elseif (str_contains($request->url(), 'admin')) {
            return redirect()->route('home');
        } else {
            return redirect()->route('home');
        }

        return response()->json([
            'status' => 401,
            'message' => 'Unauthenticated',
        ], 401);
    }
}
