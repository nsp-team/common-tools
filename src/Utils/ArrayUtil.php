<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

use ArrayAccess;
use NspTeam\Component\Tools\Collection;

class ArrayUtil
{
    /**
     * Determine whether the given value is array accessible.
     *
     * @param mixed $value
     * @return bool
     */
    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }


    /**
     * 多维数组合并成单个数组
     *
     * @param array $array
     * @return array
     */
    public static function collapse(array $array): array
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (!is_array($values)) {
                continue;
            }
            $results = array_merge($results, $values);
        }

        return $results;
    }

    /**
     * 确定提供的数组中是否存在给定的key
     *
     * @param \ArrayAccess|array $array
     * @param string|int $key
     * @return bool
     */
    public static function exists($array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }

    /**
     * 将数组转换为URL-encode 之后的请求字符串
     *
     * @param array $array
     * @return string
     */
    public static function query(array $array): string
    {
        return http_build_query($array, '', '&', PHP_QUERY_RFC3986);
    }

    /**
     * 蛇形法下划线转驼峰(首字母小写)
     *
     * @param array|object|mixed $map
     * @return array|null
     */
    public static function camel($map): ?array
    {
        if (empty($map)) {
            return null;
        }

        $tmp =[];
        foreach ($map as $camelKey => $item) {
            if (is_string($camelKey) && !is_numeric($camelKey)) {
                $camelKey = StrUtil::camel($camelKey);
            }
            if (is_array($item) ||is_object($item)) {
                $tmp[$camelKey] = self::camel($item);
            } else {
                $tmp[$camelKey] = is_numeric($item)? (int)$item : $item;
            }
        }
        return  $tmp;
    }
}