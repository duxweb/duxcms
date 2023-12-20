<?php

namespace App\Member\Service;

use App\Member\Models\MemberCollect;
use App\Member\Models\MemberComment;
use App\Member\Models\MemberPraise;
use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;

class Praise
{
    /**
     * 点赞和取消
     * @param int $userId
     * @param string $hasType
     * @param int $hasId
     * @return bool
     */
    public static function run(int $userId, string $hasType, int $hasId): bool
    {
        $info = MemberPraise::query()->where('user_id', $userId)->where('has_type', $hasType)->where('has_id', $hasId)->first();
        if ($info) {
            $info->delete();
            $info->hastable->decrement('praise');
            return false;
        }
        $data = [
            'user_id' => $userId,
            'has_type' => $hasType,
            'has_id' => $hasId,
        ];
        $info = MemberPraise::query()->create($data);
        $info->hastable()?->increment('praise');
        return true;

    }

}