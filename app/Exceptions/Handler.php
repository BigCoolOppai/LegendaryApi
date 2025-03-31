<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException; // <-- Импорт для ошибки аутентификации
use Illuminate\Auth\Access\AuthorizationException; // <-- Импорт для ошибки авторизации
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException; // <-- Импорт для 404
use Illuminate\Validation\ValidationException; // <-- Импорт для ошибок валидации
use Illuminate\Http\JsonResponse; // <-- Импорт для JSON ответа

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
        // Обработка ошибки АУТЕНТИФИКАЦИИ (Не залогинен)
        // Срабатывает, когда гость пытается получить доступ к маршруту,
        // защищенному 'auth:sanctum' (или другими auth guards)
        $this->renderable(function (AuthenticationException $e, $request) {
            // Проверяем, ожидает ли запрос JSON ответ (типично для API)
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Login failed' // Ваше сообщение для гостя
                ], 403); // <-- Стандартно 401 Unauthorized, но ТЗ просит 403. Используем 403.
                // ], 403); // Используем 403 согласно ТЗ
            }
            // Если не JSON, позволяем Laravel обработать стандартно (например, редирект на страницу логина)
            return null;
        });

        // Обработка ошибки АВТОРИЗАЦИИ (Нет прав)
        // Срабатывает, когда аутентифицированный пользователь пытается выполнить
        // действие, на которое у него нет прав (например, через Gates или Policies)
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden for you' // Ваше сообщение о запрете
                ], 403); // Статус 403 Forbidden
            }
            return null;
        });

        // Обработка ошибки НЕ НАЙДЕНО (404)
        // Срабатывает, когда маршрут или ресурс не найден
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Not found', // Ваше сообщение
                    'code' => 404
                ], 404); // Статус 404 Not Found
            }
            return null;
        });

        // Обработка ошибки ВАЛИДАЦИИ (422)
        // Срабатывает при ошибках валидации из Form Requests или $request->validate()
        $this->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => [ // Оборачиваем в ключ 'error' согласно ТЗ
                        // Код и сообщение можно брать из исключения, если они там установлены,
                        // или использовать значения по умолчанию.
                        // $e->status содержит HTTP статус (обычно 422)
                        'code' => $e->status,
                        'message' => $e->getMessage(), // Стандартное сообщение "The given data was invalid."
                        // Или ваше кастомное: 'message' => 'Validation error',
                        'errors' => $e->errors(), // Получаем массив ошибок валидации
                    ]
                ], $e->status); // Используем статус из исключения (обычно 422)
            }
            return null;
        });

        // Можно добавить обработчики для других типов исключений, если нужно

        // Отчет об ошибках (не влияет на ответ клиенту, но полезно для логов)
        $this->reportable(function (Throwable $e) {
            // Можно добавить логику репортинга, например, отправку в Sentry
        });
    }
}
