<?php

namespace App\System\Service;

class Stats
{
    /**
     * 环比计算
     * @param $currentValue
     * @param $previousValue
     * @return float|int
     */
    public static function calculateRate($currentValue, $previousValue): float|int
    {
        if (!$currentValue && !$previousValue) {
            return 0;
        }
        if ($previousValue == 0) {
            return 100;
        }
        if ($currentValue == 0) {
            return -100;
        }
        return round((($currentValue - $previousValue) / $previousValue) * 100, 2);

    }

}