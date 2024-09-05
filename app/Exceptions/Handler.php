<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use App\Exceptions\UserNotFoundException;
use App\Exceptions\UserCreationException;
use App\Exceptions\ClientNotFoundException;
use App\Exceptions\ClientCreationException;

class Handler extends ExceptionHandler
{
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

        $this->renderable(function (UserNotFoundException $e, $request) {
            return response()->json([
                'error' => 'User Not Found',
                'message' => $e->getMessage(),
            ], 404);
        });

        $this->renderable(function (UserCreationException $e, $request) {
            return response()->json([
                'error' => 'User Creation Error',
                'message' => $e->getMessage(),
            ], 422);
        });

        $this->renderable(function (ClientNotFoundException $e, $request) {
            return response()->json([
                'error' => 'Client Not Found',
                'message' => $e->getMessage(),
            ], 404);
        });

        $this->renderable(function (ClientCreationException $e, $request) {
            return response()->json([
                'error' => 'Client Creation Error',
                'message' => $e->getMessage(),
            ], 422);
        });

        // Gestion générique pour les autres exceptions
        $this->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Server Error',
                    'message' => $e->getMessage(),
                ], 500);
            }
        });
    }

    /**
     * Report or log an exception.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }
}