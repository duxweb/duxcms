<?php
declare(strict_types=1);

use Dux\App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;

/**
 * @param ResponseInterface $response
 * @param string $message
 * @param array $data
 * @param array $meta
 * @param int $code
 * @return ResponseInterface
 */
function send(ResponseInterface $response, string $message, array $data = [], array $meta = [], int $code = 200): ResponseInterface
{
    $result = [];
    $result["code"] = $code;
    $result["message"] = $message;
    $result["data"] = $data;
    $result["meta"] = $meta;
    $payload = json_encode($result, JSON_UNESCAPED_UNICODE);
    $response->getBody()->rewind();
    $response->getBody()->write($payload);
    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withStatus($code);
}

/**
 * @param ResponseInterface $response
 * @param string $message
 * @param int $code
 * @return ResponseInterface
 */
function sendText(ResponseInterface $response, string $message, int $code = 200): ResponseInterface
{
    $response->getBody()->rewind();
    $response->getBody()->write($message);
    return $response
        ->withHeader('Content-Type', 'text/html')
        ->withStatus($code);
}

/**
 * @param string $name
 * @param array $params
 * @return string
 */
function url(string $name, array $params): string
{
    return App::app()->getRouteCollector()->getRouteParser()->urlFor($name, $params);
}


/**
 * @param Collection|LengthAwarePaginator|Model|null $data
 * @param callable $callback
 * @return array
 */
function format_data(Collection|LengthAwarePaginator|Model|null $data, callable $callback): array
{
    $pageStatus = false;
    $page = 1;
    $total = 0;
    if ($data instanceof LengthAwarePaginator) {
        $pageStatus = true;
        $page = $data->currentPage();
        $total = $data->total();
        $data = $data->getCollection();
    }

    if ($data instanceof Model) {
        return [
            'data' => $callback($data),
            'meta' => []
        ];
    }
    if (!$data) {
        $data = collect();
    }

    $list = $data->map($callback)->filter()->values();
    $result = [
        'data' => $list->toArray(),
        'meta' => []
    ];

    if ($pageStatus) {
        $result['meta'] = [
            'total' => $total,
            'page' => $page
        ];
    }

    return $result;
}