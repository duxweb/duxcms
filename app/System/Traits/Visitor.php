<?php

namespace App\System\Traits;

use App\System\Models\LogVisit;
use App\System\Models\LogVisitData;
use Psr\Http\Message\ServerRequestInterface;

trait Visitor
{
    /**
     * 增加访客
     * @param string $driver
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function viewsInc(\Psr\Http\Message\ServerRequestInterface $request, string $driver = 'web'): bool
    {
        $id = $this->{$this->primaryKey};
        if (!$id) {
            return false;
        }
        \App\System\Service\Visitor::increment($request, get_called_class(), $id, $driver);
        return true;
    }


    /**
     * 删除关联内容
     * @return bool
     */
    public function viewsDel(): bool
    {
        $this->views()->delete();
        $this->viewsData(0)->delete();
        return true;
    }

    /**
     * 访问量
     */
    public function views(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(LogVisit::class, 'has', 'has_type');
    }

    /**
     * 访问数据
     */
    public function viewsData($day = 7)
    {
        $data = $this->morphMany(LogVisitData::class, 'has', 'has_type');
        if ($day) {
            $data = $data->where('date', '>=', date('Y-m-d', strtotime('-' . $day . ' day')));
        }
        return $data->orderBy('date');
    }
}