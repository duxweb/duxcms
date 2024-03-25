<?php

namespace Dux\Resources\Action;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait One
{
    public function show(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->method = 'show';
        $this->init($request, $response, $args);
        $this->event->run('init', $request, $response, $args);
        $id = $args["id"] ?: 0;

        $info = collect();
        if ($id) {
            $query = $this->model::query()->where($this->key, $id);
            $this->queryOne($query, $request, $args);
            $this->query($query);
            $this->event->run('queryOne', $query, $request, $args);
            $this->event->run('query', $query);
            $info = $query->first();
            $assign = $this->transformData($info, function ($item) {
                return [...$this->transform($item), ...$this->event->get('transform', $item)];
            });

        } else {
            $assign = [
                'data' => null,
                'meta' => []
            ];
        }

        $meta = $this->metaOne($info, $request, $args);
        $assign['meta'] = [
            ...$assign['meta'],
            ...$meta,
            ...$this->event->get('metaOne', $info, $request, $args),
        ];

        $assign['data'] = $this->filterData($this->includesOne, $this->excludesOne, [$assign['data']])[0];

        return send($response, "ok", $assign['data'], $assign['meta']);
    }

}