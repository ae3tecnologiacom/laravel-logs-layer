<?php

namespace Ae3\LaravelLogsLayer\app\Exceptions;

use Ae3\LaravelLogsLayer\app\Enums\LogsLevelsEnum;
use Throwable;
use Ae3\LaravelLogsLayer\app\Traits\LogTrait;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomExceptionHandler extends ExceptionHandler
{
    use LogTrait;

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
     * @param Throwable $exception
     * @return void
     * @throws InvalidArgumentException
     */
    public function report(Throwable $exception)
    {
        $level = $this->getExceptionLevel($exception);
        $this->logException(__METHOD__, $exception, $level);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * @param Throwable $exception
     * @return string
     */
    protected function getExceptionLevel(Throwable $exception): string
    {
        if (method_exists($exception, 'getLevel')) {
            return mb_strtolower($exception->getLevel());
        }

        return LogsLevelsEnum::ERROR;
    }

}