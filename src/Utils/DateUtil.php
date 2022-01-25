<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * DateUtil
 *
 * @package NspTeam\Component\Tools\Utils
 */
class DateUtil
{
    /**
     * 获取当前毫秒时间戳
     *
     * @return float
     */
    public static function getMicrosecond(): float
    {
        return round(microtime(true) * 1000);
    }

    /**
     * 按月份偏移
     *
     * @param  int      $num       月份
     * @param  int|null $timestamp 时间戳
     * @return false|int
     */
    public static function offsetMonth(int $num, ?int $timestamp = null)
    {
        $timestamp = $timestamp ?? time();
        return strtotime("$num month", $timestamp);
    }

    /**
     * 按小时偏移
     *
     * @param  int      $num
     * @param  int|null $timestamp
     * @return false|int
     */
    public static function offsetHour(int $num, ?int $timestamp = null)
    {
        $timestamp = $timestamp ?? time();
        return strtotime("$num hour", $timestamp);
    }
}