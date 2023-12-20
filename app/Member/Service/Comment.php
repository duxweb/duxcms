<?php

namespace App\Member\Service;

use App\Member\Models\MemberCollect;
use App\Member\Models\MemberComment;
use Dux\Handlers\ExceptionBusiness;

class Comment
{

    /**
     * 发布评论
     * @param int $userId
     * @param string $hasType
     * @param int $hasId
     * @param string|null $content
     * @param int|null $replyId
     * @return void
     */
    public static function push(int $userId, string $hasType, int $hasId, ?string $content = '', ?int $replyId = null): void
    {
        $lastInfo = MemberComment::query()->where('user_id', $userId)->where('has_type', $hasType)->where('has_id', $hasId)->orderByDesc('id')->first();
        if ($lastInfo) {
            if (now()->subMinutes(1)->lt($lastInfo->created_at)) {
                //throw new ExceptionBusiness('发送太频繁，请稍后再发送');
            }
            if ($lastInfo->content == $content) {
                throw new ExceptionBusiness('请勿发布重复评论');
            }
        }

        $data = [
            'user_id' => $userId,
            'has_type' => $hasType,
            'has_id' => $hasId,
            'content' => $content,
            'status' => 1
        ];

        if ($replyId) {
            $replyInfo = MemberComment::query()->where('id', $replyId)->where('has_type', $hasType)->where('has_id', $hasId)->where('status', 1)->exists();
            if (!$replyInfo) {
                throw new ExceptionBusiness('回复的评论不存在');
            }
            $data['parent_id'] = $replyId;
        }

        $info = MemberComment::query()->create($data);
        $info->hastable()?->increment('comment');
    }

    public static function count(int $userId, string $hasType, ?int $hasId = 0): int
    {
        $query = MemberComment::query()->where('user_id', $userId)->where('has_type', $hasType);
        if ($hasId) {
            $query->where('has_id', $hasId);
        }
        return $query->count();
    }



    public static function buildFlatTree(array $elements): array
    {
        $tree = [];
        $map = [];

        // 首先构建id到评论的映射，并初始化children数组
        foreach ($elements as &$element) {
            $element['children'] = [];
            $map[$element['id']] = &$element;
        }

        foreach ($elements as &$element) {
            if ($element['parent_id'] !== null) {
                if (isset($map[$element['parent_id']])) {
                    // 直接将评论加入到其父级评论的children数组中
                    $parentId = $map[$element['parent_id']]['parent_id'];
                    if ($parentId === null) {
                        // 如果父评论是顶级评论，直接加入
                        $element['parent'] = null;
                        $map[$element['parent_id']]['children'][] = &$element;
                    } else {
                        // 如果父评论不是顶级评论，找到顶级评论，然后加入到对应的二级评论中
                        $map[$parentId]['children'][] = &$element;
                    }
                }
            } else {
                // 顶级评论直接加入到树中
                $tree[] = &$element;
            }
        }

        return $tree;
    }

}