<?php
declare(strict_types=1);

namespace Dux\Handlers;

use Dux\App;
use Slim\Error\Renderers\JsonErrorRenderer;
use Throwable;

class ErrorJsonRenderer extends JsonErrorRenderer
{
    use ErrorRendererTrait;

    public function __construct()
    {
        $this->defaultErrorTitle = App::$bootstrap->exceptionTitle;
        $this->defaultErrorDescription = App::$bootstrap->exceptionDesc;
    }

    public function __invoke(Throwable $exception, bool $displayErrorDetails): string
    {
        $code = $exception->getCode() ?: 500;
        $error = ['code' => $code, 'message' => $this->getErrorTitle($exception), 'data' => []];
        if ($exception instanceof ExceptionData) {
            $displayErrorDetails = false;
            $error['data'] = $exception->data;
        }
        if ($displayErrorDetails) {
            do {
                $error['data'][] = $this->formatExceptionFragment($exception);
            } while ($exception = $exception->getPrevious());
        }
        return (string)json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    private function formatExceptionFragment(Throwable $exception): array
    {
        /** @var int|string $code */
        $code = $exception->getCode();
        return [
            'type' => get_class($exception),
            'code' => $code,
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ];
    }
}