<?php

namespace Dux\Resources\Action;

use Dux\App;
use Dux\Handlers\ExceptionBusiness;
use Dux\Validator\Data;
use Dux\Validator\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

trait Store
{
    public function store(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $this->method = 'store';
        $this->init($request, $response, $args);
        $this->event->run('init', $request, $response, $args);
        $id = $args["id"];

        $requestData = $request->getParsedBody() ?: [];
        $keys = array_keys($requestData);

        $validator = $this->validator($requestData, $request, $args);
        $validatorEvent = $this->event->get('validator', $requestData, $request, $args);
        $validator = array_filter(array_filter([...$validator, ...$validatorEvent]), function ($item, $key) use ($keys) {
            if (in_array($key, $keys)) {
                return true;
            }
            return false;
        }, ARRAY_FILTER_USE_BOTH);

        $data = Validator::parser($requestData, $validator);

        $format = $this->format($data, $request, $args);
        $formatEvent = $this->event->get('format', $data, $request, $args);

        $ruleData = array_filter([...$format, ...$formatEvent], function ($item, $key) use ($keys) {
            if (in_array($key, $keys)) {
                return true;
            }
            return false;
        }, ARRAY_FILTER_USE_BOTH);

        $modelData = $this->formatData($ruleData, $data);

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

        $this->storeBefore($data, $model);
        $this->event->run('storeBefore', $data, $model);

        $model->save();

        $this->storeAfter($data, $model);
        $this->event->run('storeAfter', $data, $model);

        App::db()->getConnection()->commit();

        return send($response, $this->translation($request, 'store'));
    }

    public function storeBefore(Data $data, mixed $info): void
    {
    }

    public function storeAfter(Data $data, mixed $info): void
    {
    }

}