<?php

namespace App\Member\Service;

use App\Member\Models\MemberCollect;
use App\Member\Models\MemberComment;

class Collect
{
    /**
     * 收藏和取消收藏
     * @param int $userId
     * @param string $hasType
     * @param int $hasId
     * @return void
     */
    public static function run(int $userId, string $hasType, int $hasId): void
    {
        $info = MemberCollect::query()->where('user_id', $userId)->where('has_type', $hasType)->where('has_id', $hasId)->first();
        if ($info) {
            $info->delete();
            $info->hastable->decrement('collect');
            return;
        }
        $data = [
            'user_id' => $userId,
            'has_type' => $hasType,
            'has_id' => $hasId,
        ];
        $info = MemberCollect::query()->create($data);
        $info->hastable()?->increment('collect');
    }

    public static function count(int $userId, string $hasType, ?int $hasId = 0): int
    {
        $query = MemberCollect::query()->where('user_id', $userId)->where('has_type', $hasType);
        if ($hasId) {
            $query->where('has_id', $hasId);
        }
        return $query->count();
    }

}