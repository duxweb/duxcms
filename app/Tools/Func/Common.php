<?php

/**
 * 求两个日期之间相差的天数
 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
 * @param string $date1
 * @param string $date2
 * @return number
 */
function diff_date(string $date1, string $date2): int
{
    if ($date1 > $date2) {
        $startTime = strtotime($date1);
        $endTime = strtotime($date2);
    } else {
        $startTime = strtotime($date2);
        $endTime = strtotime($date1);
    }
    $diff = $startTime - $endTime;
    $day = $diff / 86400;
    return intval($day);
}

/**
 * 昨日今日占比
 * @param float $today
 * @param float $yesterday
 * @param int $precision
 * @return float
 */
function change_percent(float $today, float $yesterday, int $precision = 2): float
{
    if($yesterday == 0){
        return min($today, 100);
    }
    return round(($today - $yesterday) / $yesterday * 100, $precision);
}