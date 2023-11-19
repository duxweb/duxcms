<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsFile;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/tools/file', name: 'tools.file', actions: ['list', 'delete'])]
class File extends Resources
{
	protected string $model = ToolsFile::class;

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $params = $request->getQueryParams();
        $app = $request->getAttribute('app');
        $query->where('has_type', $app);
        if ($params['dir_id']) {
            $query->where('dir_id', $params['dir_id']);
        }
        $query->orderByDesc('id');
    }

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "dir_name" => $item->dir->name,
            "name" => $item->name,
            "ext" => $item->ext,
            "url" => $item->url,
            "size" => human_filesize($item->size),
            "mime" => $item->mime,
            "driver" => $item->driver,
            "time" => $item->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
