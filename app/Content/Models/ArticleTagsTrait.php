<?php

namespace App\Content\Models;

trait ArticleTagsTrait
{

    /**
     * 同步标签
     * @param string|array|null $tags
     */
    public function retag(string|array|null $tags): void
    {
        $tags = $this->formatTags($tags);
        // 获取已关联标签
        $infoTags = $this->tags->pluck('name')->toArray();

        $deletions = array_diff($infoTags, $tags);
        $additions = array_diff($tags, $infoTags);

        $this->untag($deletions);

        foreach ($additions as $vo) {
            $this->addTag($vo);
        }

    }

    /**
     * 删除标签
     * @param array|string|null $tags
     */
    public function untag(array|string $tags = null): void
    {
        if ($tags) {
            $tags = $this->formatTags($tags);
        }else {
            $tags = $this->tags->pluck('name')->toArray();
        }
        foreach ($tags as $vo) {
            $tagInfo = $this->tags()->where('name', $vo)->first();
            // 移除关联
            $this->tags()->detach($tagInfo->id);
            if ($tagInfo->count <= 1) {
                // 剩余一个删除标签
                $tagInfo->delete();
            } else {
                $tagInfo->decrement('count');
            }
        }
    }

    /**
     * 增加单个标签
     * @param $tag
     */
    private function addTag($tag) {
        $tagName = trim($tag);

        if(strlen($tagName) == 0) {
            return;
        }

        $tagInfo = ArticleTags::query()->where('name', $tagName)->first();
        if ($tagInfo) {
            // 递增引用次数
            $tagInfo->increment('count');
            $tagId = $tagInfo->id;
        } else {
            // 创建应用
            $tagInfo = new ArticleTags();
            $tagInfo->name = $tagName;
            $tagInfo->count = 1;
            $tagInfo->view = 1;
            $tagInfo->save();
            $tagId = $tagInfo->id;
        }
        $this->tags()->attach($tagId);
    }


    /**
     * 格式化标签
     * @param array|string $tags
     * @return array
     */
    private function formatTags(array|string $tags): array
    {
        if (!$tags) {
            return [];
        }
        if (is_string($tags)) {
            $tags = explode(',', $tags);
        }

        if (is_array($tags)) {
            $tags = array_filter($tags);
        }
        $tags = array_map('trim', $tags);

        return array_values($tags);
    }
}