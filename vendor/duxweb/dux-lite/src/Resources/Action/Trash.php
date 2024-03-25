<?php

namespace Dux\Resources\Action;

use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait Trash
{
    public function trash(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->method = 'trash';
        $this->init($request, $response, $args);
        $this->event->run('init', $request, $response, $args);
        $id = $args["id"];

        App::db()->getConnection()->beginTransaction();

        $query = $this->model::query()->where($this->key, $id);
        $this->queryOne($query, $request, $args);
        $this->query($query);
        $this->event->run('queryOne', $query, $request, $args);
        $this->event->run('query', $query);

        $model = $query->withTrashed()->first();
        if (!$model) {
            throw new ExceptionBusiness(__("message.emptyData", "common"));
        }

        $this->trashBefore($model);
        $this->event->run('trashBefore', $model);

        $model->forceDelete();

        $this->trashAfter($model);
        $this->event->run('trashAfter', $model);

        App::db()->getConnection()->commit();

        return send($response, $this->translation($request, 'trash'));
    }

    public function trashBefore(mixed $info): void
    {
    }

    public function trashAfter(mixed $info): void
    {
    }

}