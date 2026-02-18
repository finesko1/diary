<?php

use App\Exceptions\ApiException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReportDuplicates();

        $exceptions->render(function (ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors
            ], $e->status);
        });

        $exceptions->render(function (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Resource not found',
            ], 404);
        });

        $exceptions->render(function (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Forbidden',
            ], 403);
        });

        $exceptions->render(function (AuthenticationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Unauthorized',
            ], 401);
        });

        $exceptions->render(function (QueryException $e) {
            $sqlError = $e->errorInfo[1] ?? null;
            $sqlMessage = $e->getMessage();

            // 1062 - Duplicate entry
            if ($sqlError === 23505) {
                return response()->json([
                    'success' => false,
                    'message' => 'Запись с такими данными уже существует',
                    'sql_error' => 'duplicate_entry',
                ], 409);
            }

            // 1451 - Cannot delete or update a parent row (foreign key constraint)
            if ($sqlError === 23503) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить запись, так как она используется в других записях',
                    'sql_error' => 'foreign_key_constraint'
                ], 409);
            }

            // 1452 - Cannot add or update a child row (foreign key constraint)
            if ($sqlError === 23502) {
                return response()->json([
                    'success' => false,
                    'message' => 'Указанная связанная запись не существует',
                    'sql_error' => 'foreign_key_not_found'
                ], 404);
            }

            // 42601 - syntax error
            if ($sqlError === '42601') {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка в запросе к базе данных',
                    'sql_error' => 'syntax_error'
                ], 500);
            }

            // 28000 - invalid authorization (проблемы с подключением)
            if ($sqlError === '28000' || str_contains($sqlMessage, 'no pg_hba.conf entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка подключения к базе данных',
                    'sql_error' => 'connection_auth_failed'
                ], 500);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ошибка базы данных. Пожалуйста, попробуйте позже',
                'sql_error' => 'database_error'
            ], 500);
        });

        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        });

        $exceptions->render(function (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Internal server error',
            ], 500);
        });
    })->create();
