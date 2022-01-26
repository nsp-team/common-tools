<?php

namespace NspTeam\Component\Tools\Core\Date;

use DateTime;

/**
 * Date
 * @package NspTeam\Component\Tools\Core\Date
 * @author mxp
 * @license Apache
 * @category date
 * @link
 */
final class Date
{
    /**
     * 日期/时间
     * @var string 指定的日期/时间
     */
    protected $date;

    /**
     * DateTime
     * @var DateTime DateTime对象
     */
    public $dateTimeInstance;

    /**
     * @param string|null $date 日期|时间
     * @throws \Exception
     */
    public function __construct(?string $date = null)
    {
        if (!isset($date)) {
            $date = (new DateTime)->format('Y-m-d H:i:s.u');
        } elseif (is_numeric($date)) {
            throw new \InvalidArgumentException('date 必须是正常的字符串日期/时间，不支持时间戳');
        }
        $this->date = $date;
        $this->dateTimeInstance = new DateTime($date);
    }

    /**
     * 指定日期/时间的DateTime对象
     * @return DateTime
     */
    public function getDateTimeInstance(): DateTime
    {
        return $this->dateTimeInstance;
    }

    /**
     * 测试$when此日期是否在指定日期之后
     * @param string $when
     * @return bool
     * @throws \Exception
     */
    public function after(string $when): bool
    {
        $timestamp = (new DateTime($when))->getTimestamp();

        return $this->getTimestamp() < $timestamp;
    }

    public function getTimestamp(): int
    {
        return $this->dateTimeInstance->getTimestamp();
    }

    /**
     * 测试$when此日期是否在指定日期之之前
     * @param string $when
     * @return bool
     * @throws \Exception
     */
    public function before(string $when): bool
    {
        $timestamp = (new DateTime($when))->getTimestamp();
        return $timestamp < $this->getTimestamp();
    }

    /**
     * 测试此日期是否与指定日期相等
     * @param string $when
     * @return bool
     * @throws \Exception
     */
    public function equal(string $when): bool
    {
        $timestamp = (new DateTime($when))->getTimestamp();

        return $this->getTimestamp() === $timestamp;
    }

    /**
     * 指定日期/时间的详细信息
     * @return array
     */
    public function getDateParse(): array
    {
        $parse = date_parse($this->date);
        return [
            'year' => $parse['year'],
            'month' => $parse['month'],
            'day' => $parse['day'],
            'hour' => $parse['hour'],
            'minute' => $parse['minute'],
            'second' => $parse['second'],
        ];
    }

    /**
     * 指定日期/时间的年数
     * @return int
     */
    public function getYear(): int
    {
        return $this->getDateParse()['year'];
    }

    /**
     * 指定日期/时间的月份数
     * @return int
     */
    public function getMonth(): int
    {
        return $this->getDateParse()['month'];
    }

    /**
     * 指定日期/时间的天数
     * @return int
     */
    public function getDay(): int
    {
        return $this->getDateParse()['day'];
    }

    /**
     * 指定日期/时间的小时数
     * @return int
     */
    public function getHour(): int
    {
        return $this->getDateParse()['hour'];
    }

    /**
     * 指定日期/时间的分钟数
     * @return int
     */
    public function getMinute(): int
    {
        return $this->getDateParse()['minute'];
    }

    /**
     * 指定日期/时间的秒数
     * @return int
     */
    public function getSecond(): int
    {
        return $this->getDateParse()['second'];
    }

    /**
     * 指定日期时间的(部分)微秒, 长度6
     * @return int
     */
    public function getMicrosTimeMantissa(): int
    {
        return (int)$this->dateTimeInstance->format('u');
    }

    /**
     * 时间戳.微妙
     * @return string
     */
    public function currentTimeMicros(): string
    {
        $mantissaOfMicros = bcdiv($this->dateTimeInstance->format('u'), '1000000', 6);
        return bcadd((string)$this->getTimestamp(), $mantissaOfMicros, 6);
    }

    /**
     * 时间戳.毫妙
     * @return string
     */
    public function currentTimeMillis(): string
    {
        $mantissaOfMillis = bcdiv($this->dateTimeInstance->format('u'), '1000000', 3);
        return bcadd((string)$this->getTimestamp(), $mantissaOfMillis, 3);
    }
}