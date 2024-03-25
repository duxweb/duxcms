<?php
declare(strict_types=1);

namespace Dux\Validator;

// https://github.com/vlucas/valitron
use Dux\Handlers\ExceptionValidator;

class Validator
{

    /**
     * 数据验证
     * @param array|object|null $data data array
     * @param array $rules ["name" => ["rule", "message"]]
     * @return Data
     */
    public static function parser(null|array|object $data, array $rules): Data
    {
        //  $role = [
        //      "name" => ["rule", "message"]
        //  ];
        $v = new \Valitron\Validator($data ?: []);
        foreach ($rules as $key => $item) {
            if (empty($item)) {
                continue;
            }
            if (!is_array($item[0])) {
                $datas = [$item];
            } else {
                $datas = $item;
            }
            $keys = explode('#', $key);
            $key = trim($keys[0]);
            foreach ($datas as $vo) {
                $message = last($vo);
                $params = array_slice($vo, 1, -1);
                $v->rule($vo[0], $key, ...$params)->message($message);
            }
        }
        if (!$v->validate()) {
            throw new ExceptionValidator($v->errors());
        }
        $dataObj = new Data();
        foreach ($data as $k => $v) {
            $dataObj->$k = $v;
        }
        return $dataObj;
    }


    /**
     * 数据规则
     * @param array $fields
     * @return array
     */
    public static function rule(array $fields): array
    {
        $validators = [];
        foreach ($fields as $field) {
            $rules = json_decode($field['setting']['rules'] ?: '', true);
            if ($field['required']) {
                $rules[] = ['required' => true, 'message' => __('validate.placeholder', 'common', [
                    '%name%' => $field['label']
                ])];
            }
            $ruleList = [];
            foreach ($rules as $rule) {
                foreach ($rule as $key => $vo) {
                    if ($key == 'message') {
                        continue;
                    }
                    $cover = match ($key) {
                        'boolean' => 'boolean',
                        'date' => 'date',
                        'email' => 'email',
                        'enum' => function ($field, $value, array $params, array $fields) use ($rule) {
                            return in_array($value, (array)$rule);
                        },
                        'idcard' => ['regex', '/^(\\d{18,18}|\\d{15,15}|\\d{17,17}x)$/i'],
                        'length' => ['length', $vo],
                        'min' => ['lengthMin', $vo],
                        'max' => ['lengthMax', $vo],
                        'number' => 'numeric',
                        'pattern' => ['regex', $vo],
                        'required' => ['required', $vo],
                        'telnumber' => ['regex', '/^1[3-9]\d{9}$/'],
                        'url' => 'url',
                        'default' => null
                    };
                    if ($cover) {
                        $ruleList[] = is_array($cover) ? [...$cover, $rule['message']] : [$cover, $rule['message']];
                    }
                }
            }
            if ($ruleList) {
                $validators[$field['name']] = $ruleList;
            }
        }

        return $validators;
    }

}