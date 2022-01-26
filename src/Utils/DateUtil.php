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
     * 获取当前时间戳.毫秒
     * @return string
     */
    public static function currentTimeMillis():string
    {
        return sprintf("%.3f",microtime(true));
    }

    /**
     * 获取当前时间戳.微妙
     * @return string
     */
    public static function currentTimeMicros(): string
    {
        // 微秒部分、Unix时间戳(秒数)
        [$microsecond, $unixTimestamp] = explode(' ', microtime());
        return bcadd($microsecond, $unixTimestamp, 6);
    }

    /**
     * 生成时间戳
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @return false|int
     */
    public static function mkTimestamp(int $year, int $month, int $day, int $hour, int $minute, int $second)
    {
        if (!checkdate($month, $day,$year)) {
            return null;
        }
        return mktime($hour, $minute, $second,$month, $day, $year);
    }

    /**
     * 返回一个根据 timestamp 得出的包含有日期信息的关联数组。如果没有给出时间戳则认为是当前本地时间。
     * 星期中第几天
     * 月份中第几天
     * 一年中第几天
     * @param int|null $timestamp
     * @return array
     */
    public static function getDateParse(?int $timestamp=null): array
    {
        return getdate($timestamp);
    }

    /**
     * 本月的总天数
     * @param int|null $timestamp
     * @return int
     */
    public static function getTotalDaysOfMonth(?int $timestamp=null):int
    {
        return idate('t', $timestamp ?? time());
    }

    /**
     * 判断是否闰年(1-闰年 0-平年)
     * @param int|null $timestamp
     * @return bool
     */
    public static function isLeapYear(?int $timestamp=null): bool
    {
        $l = idate('L', $timestamp ?? time());
        return $l === 1;
    }

    /**
     * 是否启用了夏时制
     * @param int|null $timestamp
     * @return bool
     */
    public static function isDaylightSavingTime(?int $timestamp=null): bool
    {
        $l = idate('I', $timestamp ?? time());
        return $l === 1;
    }

    /**
     * 星期中的第几天（0 (周日) 到 6 (周六)）
     * @param int|null $timestamp
     * @return int
     */
    public static function getWeekend(?int $timestamp=null):int
    {
        return idate('w', $timestamp ?? time());
    }

    /**
     * （忽略秒钟）当前时分是否在 [start时分,end时分) 之间,忽略秒钟(处理存在跨天的情况)
     * @param string $beginTime HH:mm:ss
     * @param string $endTime HH:mm:ss
     * @param string|null $currentTime
     * @return bool
     */
    public static function currentTimeBetweenAAndBIgnoreSec(string $beginTime, string $endTime, ?string $currentTime = null): bool
    {
        $beginTimeDateParse = date_parse($beginTime);
        $endTimeDateParse = date_parse($endTime);
        $currentTimeDateParse = date_parse($currentTime ?? date('Y-m-d H:i:s'));
        ['hour' => $beginTimeHour, 'minute' => $beginTimeMinute] = $beginTimeDateParse;
        ['hour' => $endTimeHour, 'minute' => $endTimeMinute] = $endTimeDateParse;
        ['hour' => $currentTimeHour, 'minute' => $currentTimeMinute] = $currentTimeDateParse;


        $operate = false;
        // 一天内24小时制计算
        if ($beginTimeHour < $endTimeHour) {
            if ($currentTimeHour > $beginTimeHour && $currentTimeHour < $endTimeHour) {
                $operate = true;
            } elseif ($currentTimeHour === $beginTimeHour && $currentTimeMinute >= $beginTimeMinute) {
                $operate = true;
            } elseif ($currentTimeHour === $endTimeHour && $currentTimeMinute < $endTimeMinute) {
                $operate = true;
            }
        } elseif ($beginTimeHour === $endTimeHour) {
            // 2种情况 1.同一天 2.跨天
            // 根据分钟决定是case1 or case2
            if ($beginTimeMinute < $endTimeMinute) {
                // case1 同一天(6:20~ 6:50)
                if ($currentTimeHour === $beginTimeHour && $currentTimeMinute >= $beginTimeMinute && $currentTimeMinute < $endTimeMinute) {
                    $operate = true;
                }
            } elseif ($beginTimeMinute > $endTimeMinute) {
                // case2 跨天(6:50~ 6:20)
                if ($currentTimeHour === $beginTimeHour) {
                    if ($currentTimeMinute >= $beginTimeMinute || $currentTimeMinute < $endTimeMinute) {
                        $operate = true;
                    }
                } elseif ($currentTimeHour > $beginTimeHour || $currentTimeHour < $endTimeHour) {
                    $operate = true;
                }
            }
        } elseif ($beginTimeHour > $endTimeHour) {
            // 跨天
            if ($currentTimeHour > $beginTimeHour || $currentTimeHour < $endTimeHour) {
                $operate = true;
            } elseif ($currentTimeHour === $beginTimeHour && $currentTimeMinute >= $beginTimeMinute) {
                $operate = true;
            } elseif ($currentTimeHour === $endTimeHour && $currentTimeMinute < $endTimeMinute) {
                $operate = true;
            }
        }

        return $operate;
    }

    /**
     * 当前时分秒时间是否在 [start时分秒,end时分秒) 之间,(处理存在跨天的情况)
     * @param string $beginTime 开始时间
     * @param string $endTime 结束时间
     * @param string|null $currentTime
     * @return bool
     */
    public static function currentTimeBetweenAAndB(string $beginTime, string $endTime, ?string $currentTime = null): bool
    {
        $beginTimeDateParse = date_parse($beginTime);
        $endTimeDateParse = date_parse($endTime);
        $currentTimeDateParse = date_parse($currentTime ?? date('Y-m-d H:i:s'));
        ['hour' => $beginTimeHour, 'minute' => $beginTimeMinute, 'second' => $beginTimeSecond] = $beginTimeDateParse;
        ['hour' => $endTimeHour, 'minute' => $endTimeMinute, 'second' => $endTimeSecond] = $endTimeDateParse;
        ['hour' => $currentTimeHour, 'minute' => $currentTimeMinute, 'second' => $currentTimeSecond] = $currentTimeDateParse;


        $operate = false;
        // 一天内24小时制计算 (2:20:19~ 9:50:44)
        if ($beginTimeHour < $endTimeHour) {
            if ($currentTimeHour > $beginTimeHour && $currentTimeHour < $endTimeHour) {
                $operate = true;
            } elseif (
                ($currentTimeHour === $beginTimeHour && $currentTimeMinute > $beginTimeMinute) ||
                ($currentTimeHour === $endTimeHour && $currentTimeMinute < $endTimeMinute)
            ) {
                $operate = true;
            } elseif (
                ($currentTimeHour === $beginTimeHour && $currentTimeMinute === $beginTimeMinute && $currentTimeSecond >= $beginTimeSecond) ||
                ($currentTimeHour === $beginTimeHour && $currentTimeMinute === $endTimeMinute && $currentTimeSecond < $endTimeMinute)
            ) {
                $operate = true;
            }
        } elseif ($beginTimeHour === $endTimeHour) {
            // 2种情况 1.同一天 2.跨天
            // 根据分钟决定是case1 or case2
            if ($beginTimeMinute < $endTimeMinute) {
                // case1 (6:20:17~ 6:50:44)
                if ($currentTimeHour === $beginTimeHour && $currentTimeMinute > $beginTimeMinute && $currentTimeMinute < $endTimeMinute) {
                    $operate = true;
                } elseif (
                    ($currentTimeHour === $beginTimeHour && $currentTimeMinute === $beginTimeMinute && $currentTimeSecond >= $beginTimeSecond) ||
                    ($currentTimeHour === $endTimeHour && $currentTimeMinute === $endTimeMinute && $currentTimeSecond < $endTimeSecond)
                ) {
                    $operate = true;
                }
            } elseif ($beginTimeMinute > $endTimeMinute) {
                // case2 (6:44:20 ~ 第二天的6:20:59)
                if ($currentTimeHour === $beginTimeHour) {
                    if ($currentTimeMinute > $beginTimeMinute || $currentTimeMinute < $endTimeMinute) {
                        $operate = true;
                    }
                } elseif ($currentTimeHour > $beginTimeHour || $currentTimeHour < $endTimeHour) {
                    $operate = true;
                }
            } elseif ($beginTimeMinute === $endTimeMinute) {
                // 同小时同分钟，秒种不同的情况下，根据秒种决定是case1 or case2
                // ① (6:20:17~ 6:20:44)  当前时间在2者之间，只是秒不同
                // ② (6:44:11 ~ 第二天的6:44:10)

                //小时相同
                if ($currentTimeHour === $beginTimeHour) {
                    // 同一天的case, 小时相同，分钟相同，秒不同，必须在开始和结束之间
                    if ($currentTimeMinute === $beginTimeMinute &&
                        ($currentTimeSecond >= $beginTimeSecond && $currentTimeSecond < $endTimeSecond)
                    ) {
                        $operate = true;
                    } elseif (
                        ($currentTimeMinute === $beginTimeMinute && $currentTimeSecond >= $beginTimeSecond) ||
                        ($currentTimeMinute === $endTimeMinute && $currentTimeSecond < $endTimeSecond)
                    ) {
                        // 跨天的case，小时相同，分钟相同, 秒不同
                        $operate = true;
                    } elseif ($currentTimeMinute > $beginTimeMinute || $currentTimeMinute < $endTimeMinute) {
                        // 跨天的case，小时相同，分钟不同
                        $operate = true;
                    }
                } elseif ($currentTimeHour > $beginTimeHour || $currentTimeHour < $endTimeHour) {
                    // 跨天的case
                    $operate = true;
                }
            }
        } elseif ($beginTimeHour > $endTimeHour) {
            // 跨天
            if ($currentTimeHour > $beginTimeHour || $currentTimeHour < $endTimeHour) {
                $operate = true;
            } elseif (
                ($currentTimeHour === $beginTimeHour && $currentTimeMinute > $beginTimeMinute) ||
                ($currentTimeHour === $endTimeHour && $currentTimeMinute < $endTimeMinute)
            ) {
                $operate = true;
            } elseif (
                ($currentTimeHour === $beginTimeHour && $currentTimeMinute === $beginTimeMinute && $currentTimeSecond >= $beginTimeSecond) ||
                ($currentTimeHour === $endTimeHour && $currentTimeMinute === $endTimeMinute && $currentTimeSecond < $endTimeMinute)
            ) {
                $operate = true;
            }
        }

        return $operate;
    }

    /**
     * 按月份偏移
     *
     * @param int $num 月份
     * @param int|null $timestamp 时间戳
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
     * @param int $num
     * @param int|null $timestamp
     * @return false|int
     */
    public static function offsetHour(int $num, ?int $timestamp = null)
    {
        $timestamp = $timestamp ?? time();
        return strtotime("$num hour", $timestamp);
    }
}