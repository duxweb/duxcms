<?php

namespace Dux\Resources\Action;

use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait  Edit
{
    public function edit(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->method = 'edit';
        $this->init($request, $response, $args);
        $this->event->run('init', $request, $response, $args);
        $id = (int)$args["id"];

        $requestData = $request->getParsedBody() ?: [];
        $validator = $this->validator($requestData, $request, $args);
        $validatorEvent = $this->event->get('validator', $requestData, $request, $args);

        $data = Validator::parser($requestData, [...$validator, ...$validatorEvent]);

        $format = $this->format($data, $request, $args);
        $formatEvent = $this->event->get('format', $data, $request, $args);
        $modelData = $this->formatData([...$format, ...$formatEvent], $data);

        App::db()->getConnection()->beginTransaction();
        $query = $this->model::query()->where($this->key, $id);
        $this->queryOne($query, $request, $args);
        $this->query($query);

        $this->event->run('queryOne', $query, $request, $args);
        $this->event->run('query', $query);

        $model = $query->first();
        if (!$model) {
            throw new ExceptionBusiness(__("message.emptyData", "common"));
        }

        foreach ($modelData as $key => $vo) {
            $model->$key = $vo;
        }

        $this->editBefore($data, $model);
        $this->event->run('editBefore', $model);

        $model->save();

        $this->editAfter($data, $model);
        $this->event->run('editAfter', $model);

        App::db()->getConnection()->commit();

        return send($response, $this->translation($request, 'edit'));
    }

    public function editBefore(Data $data, mixed $info): void
    {
    }

    public function editAfter(Data $data, mixed $info): void
    {
    }
}