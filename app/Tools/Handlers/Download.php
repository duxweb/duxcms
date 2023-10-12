<?php

namespace App\Tools\Handlers;

use App\Tools\Models\ToolsDownload;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Download
{

    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $app = $request->getAttribute('app');
        $auth = $request->getAttribute('auth');
        $list = ToolsDownload::query()->where('has_user', $app)->where('has_id', $auth['id'])->paginate(10);

        $data = format_data($list, function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'url' => $item->url,
                'time' => $item->created_at->format('Y-m-d H:i:s'),
            ];
        });
        return send($response, 'ok', $data);
    }
    
}