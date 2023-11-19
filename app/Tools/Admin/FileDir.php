<?php

declare(strict_types=1);

namespace App\Tools\Admin;

use App\Tools\Models\ToolsFileDir;
use Dux\Resources\Action\Resources;
use Dux\Resources\Attribute\Resource;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Psr\Http\Message\ServerRequestInterface;

#[Resource(app: 'admin',  route: '/tools/fileDir', name: 'tools.fileDir')]
class FileDir extends Resources
{
	protected string $model = ToolsFileDir::class;

    public function transform(object $item): array
    {
        return [
            "id" => $item->id,
            "name" => $item->name,
        ];
    }

    public function queryMany(Builder $query, ServerRequestInterface $request, array $args): void
    {
        $app = $request->getAttribute('app');
        $query->where('has_type', $app);
    }

    public function validator(array $data, ServerRequestInterface $request, array $args): array
	{
		return [
		    "name" => ["required", __('tools.fileDir.validator.name', 'manage')],
		];
	}

    public function format(Data $data, ServerRequestInterface $request, array $args): array
	{
        $app = $request->getAttribute('app');
		return [
		    "name" => $data->name,
            "has_type" => $app
		];
	}
}
