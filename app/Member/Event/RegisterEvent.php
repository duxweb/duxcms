<?php

namespace App\Member\Event;

use App\Member\Models\MemberUser;
use Dux\Handlers\ExceptionBusiness;
use Dux\Validator\Data;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Contracts\EventDispatcher\Event;
use \Illuminate\Database\Eloquent\Model;

class RegisterEvent extends Event
{
    /**
     * @param MemberUser $info 用户信息
     */
    public function __construct(public MemberUser $info, public ?Data $params = null)
    {
    }

}