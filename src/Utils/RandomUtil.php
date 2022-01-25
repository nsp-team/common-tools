<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * RandomUtil
 *
 * @package NspTeam\Component\Tools\Utils
 */
class RandomUtil
{
    /**
     * 获得随机数[0, 2^32)
     *
     * @return int
     * @throws \Exception
     */
    public static function randomInt(): int
    {
        return random_int(0, mt_getrandmax());
    }

    /**
     * 随机浮点数
     *
     * @param  int $min
     * @param  int $max
     * @return float
     */
    public static function randomFloat(int $min = 0, int $max = 1): float
    {
        return $min + mt_rand() / mt_getrandmax() * ($max - $min);
    }
}