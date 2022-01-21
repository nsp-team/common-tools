<?php

namespace NspTeam\Component\Tools\Utils;

use Doctrine\Inflector\InflectorFactory;

/**
 * doctrine/inflector
 * @package NspTeam\Component\Tools\Utils
 * InflectorUtil
 */
class InflectorUtil
{
    public static function make(): \Doctrine\Inflector\Inflector
    {
        return InflectorFactory::create()->build();
    }
}

