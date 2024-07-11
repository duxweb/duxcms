<?php

namespace App\Content\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * 文章事件
 */
class QrcodeEvent extends Event
{

    public array $params = [
        'type' => 'article'
    ];

    public function __construct(public mixed $info, public int $userId)
    {
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function setParams($params): void
    {
        $this->params = [...$this->params, $params];
    }

}