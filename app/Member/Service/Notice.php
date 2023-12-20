<?php

namespace App\Member\Service;

use App\Member\Models\MemberNotice;
use App\Member\Models\MemberNoticeRead;
use App\Member\Models\MemberUser;
use DI\DependencyException;
use DI\NotFoundException;
use Dux\App;

class Notice
{

    /**
     * 发送通知
     * @param array $userIds
     * @param string $title
     * @param string $desc
     * @param string $url
     * @param string $image
     * @return void
     * @throws DependencyException
     * @throws NotFoundException
     */
    public static function send(array $userIds, string $title, string $desc, string $url = '', string $image = ''): void
    {
        $type = $userIds ? 0 : 1;

        $data = [];
        if ($type) {
            // 全部
            $data[] = [
                'type' => $type,
                'user_id' => null,
                'title' => $title,
                'desc' => $desc,
                'image' => $image,
                'url' => $url,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            // 用户
            foreach ($userIds as $userId) {
                $data[] = [
                    'type' => $type,
                    'user_id' => $userId,
                    'title' => $title,
                    'desc' => $desc,
                    'image' => $image,
                    'url' => $url,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        $list = array_chunk($data, 1000);
        foreach ($list as $vo) {
            App::db()->getConnection()->table('member_notice')->insert($vo);
        }
    }

    public static function list(int $userId): array
    {
        $userInfo = MemberUser::query()->find($userId);
        $notice = MemberNotice::query()
            ->where('user_id', $userId)
            ->orWhere('type', 1)
            ->where('created_at', '>=', $userInfo->created_at->format('Y-m-d H:i:s'))
            ->paginate(15);

        $result = format_data($notice, function ($data) {
            return [
                'id' => $data->id,
                'title' => $data->title,
                'desc' => $data->desc,
                'image' => $data->image,
                'url' => $data->url,
                'created_at' => $data->created_at->format('Y-m-d H:i:s')
            ];
        });

        $ids = array_filter(array_column((array)$result['data'], 'id'));

        $readData = MemberNoticeRead::query()->whereIn('notice_id', $ids)->where('user_id', $userId)->pluck('notice_id')->toArray();
        foreach ($result['data'] as &$vo) {
            $vo['read'] = in_array($vo['id'], $readData);
        }
        return $result;
    }

    public static function read(int $userId, array $ids = []): void
    {
        if (!$ids) {
            $ids = MemberNotice::query()->where('user_id', $userId)->orWhere('type', 1)->pluck('id')->toArray();
        }
        $noticeIds = MemberNoticeRead::query()->whereIn('notice_id', $ids)->where('user_id', $userId)->pluck('notice_id')->toArray();
        $result = array_diff($ids, $noticeIds);

        $data = [];
        foreach ($result as $id) {
            $data['user_id'] = $userId;
            $data['notice_id'] = $id;
        }
        MemberNoticeRead::query()->insert($data);
    }

}