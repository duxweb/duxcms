<?php

namespace Dux\Resources\Action;

use Illuminate\Database\Eloquent\Builder;
use Kalnoy\Nestedset\Collection;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait Many
{
    public function list(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->method = 'many';

        $this->init($request, $response, $args);
        $this->event->run('init', $request, $response, $args);
        $queryParams = $request->getQueryParams();

        if (!isset($queryParams["pageSize"])) {
            $this->pagination['status'] = false;
        }

        $limit = 0;
        if ($this->pagination['status']) {
            $limit = $queryParams["pageSize"] ?: $this->pagination['pageSize'];
        }

        /**
         * @var $query Builder
         */
        $query = $this->model::query();

        $key = $queryParams['id'];
        if ($key) {
            $query->where($this->key, $key);
        }

        $sorts = $this->getSorts($queryParams);
        foreach ($sorts as $key => $sort) {
            $query->orderBy($key, $sort);
        }

        $this->queryMany($query, $request, $args);
        $this->query($query);

        $this->event->run('queryMany', $query, $request, $args);
        $this->event->run('query', $query);

        $keys = array_filter(explode(',', $queryParams['ids']));
        if (isset($queryParams['ids'])) {
            $query->whereIn($this->key, $keys);
        }

        if ($keys) {
            $bindings = implode(',', array_fill(0, count($keys), '?'));
            $query->reorder()->orderByRaw("FIELD($this->key, $bindings)", $keys);
        }

        if ($this->pagination['status']) {
            $result = $query->paginate($limit);
        }else {
            if ($this->tree) {
                $result = $query->get()->toTree();
            }else {
                $result = $query->get();
            }
        }

        $assign = $this->transformData($result, function ($item): array {
            return [...$this->transform($item), ...$this->event->get('transform', $item)];
        });

        $assign['data'] = $this->filterData($this->includesMany, $this->excludesMany, $assign['data']);

        $meta = $this->metaMany($result, (array)$result['data'], $request, $args);

        $assign['meta'] = [
            ...$assign['meta'],
            ...$meta,
            ...$this->event->get('metaMany', (array)$result['data'], $request, $args),
        ];

        return send($response, "ok", $assign['data'], $assign['meta']);
    }

}