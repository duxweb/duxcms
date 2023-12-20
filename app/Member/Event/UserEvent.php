<?php

namespace App\Member\Event;

use Symfony\Contracts\EventDispatcher\Event;

class UserEvent extends Event
{

    // 用户附加数据
    private array $data = [];

    /**
     * @param object $info 用户表数据
     */
    public function __construct(public object $info)
    {
    }


    /**
     * 获取资料
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 设置资料
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        $this->data = [...$this->data, ...$data];
    }

}