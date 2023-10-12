<?php

namespace App\System\Event;

use Carbon\Carbon;
use Symfony\Contracts\EventDispatcher\Event;

class StatsCardEvent extends Event
{

    private array $cards = [];

    public function setCard(
        string $name,
        string $unit,
        string $num,
        string $contrastName = '',
        string $contrastNum = '',
        string $rate = '',
    ): void
    {
        $this->cards[] = [
            'name' => $name,
            'unit' => $unit,
            'num' => $num,
            'contrast_name' => $contrastName,
            'contrast_num' => $contrastNum,
            'rate' => $rate
        ];
    }

    public function getCards(): array
    {
        return $this->cards;
    }

    /**
     * 卡片数据格式化
     * @param array $dateData
     * @param int $decimal
     * @return array
     */
    public function cardFormat(array $dateData, int $decimal = 2): array
    {
        $yesterdayDay = Carbon::yesterday()->toDateString();
        $day = Carbon::now()->toDateString();
        $data = [];
        foreach ($dateData as $vo) {
            $data[$vo['label']] = $vo['value'];
        }

        $dayNum = $data[$day] ?: 0;
        $yesterdayNum = $data[$yesterdayDay] ?: 0;

        return [
            'day' => bc_format($dayNum, $decimal),
            'yesterday' => bc_format($yesterdayNum, $decimal),
            'rate' => $this->cardRate((float)$dayNum, (float)$yesterdayNum)
        ];

    }

    /**
     * 增长比计算
     * @param float $day
     * @param float $yesterday
     * @return string
     */
    public function cardRate(float $day, float $yesterday): string
    {
        if ($yesterday) {
            $rate = ($day - $yesterday) / $yesterday * 100;
        } else {
            if ($day) {
                $rate = 100;
            } else {
                $rate = 0;
            }
        }
        return bc_format(round($rate, 2), 2);
    }

}