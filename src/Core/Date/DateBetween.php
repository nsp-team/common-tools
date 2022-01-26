<?php
declare(strict_types =1);

namespace NspTeam\Component\Tools\Core\Date;

/**
 * DateBetween
 * @package NspTeam\Component\Tools\Core\Date
 *
 */
class DateBetween
{
    /**
     * 开始日期
     * @var Date
     */
    protected $beginDate;

    /**
     * 结束日期
     * @var Date
     */
    protected $endDate;

    public function __construct(Date $beginDate, Date $endDate)
    {
        $this->beginDate = $beginDate;
        $this->endDate = $endDate;
    }

    /**
     * 创建, 在前的日期做为起始时间，在后的做为结束时间，间隔只保留绝对值正数
     * @param Date $beginDate
     * @param Date $endDate
     * @param bool $isAbs
     * @return DateBetween
     */
    public static function create(Date $beginDate, Date $endDate, bool $isAbs): DateBetween
    {
        // begin must be < end
        return new self($beginDate, $endDate);
    }

    /**
     * 判断两个日期相差的时长<br>
     *
     * @param int $unit 相差的单位秒
     * @return float|int 返回 给定单位的时长差
     */
    public function between(int $unit = 1)
    {
        $diff = $this->endDate->getTimestamp() - $this->beginDate->getTimestamp();
        return $diff / $unit;
    }
}