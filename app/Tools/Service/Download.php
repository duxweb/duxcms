<?php

namespace App\Tools\Service;

use App\Tools\Models\ToolsDownload;

class Download
{

    /**
     * 添加文件到下载
     * @param string $userType
     * @param string $userId
     * @param string $title
     * @param string $url
     * @return void
     */
    public function add(string $userType, string $userId, string $title, string $url): void
    {
        ToolsDownload::query()->create([
            'user_type' => $userType,
            'user_id' => $userId,
            'title' => $title,
            'url' => $url
        ]);
    }

}