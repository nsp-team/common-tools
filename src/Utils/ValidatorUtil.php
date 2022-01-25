<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * ValidatorUtil
 *
 * @package NspTeam\Component\Tools\Utils
 * @since   0.0.1
 */
class ValidatorUtil
{
    /**
     * 验证手机号是否正确
     *
     * @param  string $value
     * @return bool
     */
    public static function isPhone(string $value): bool
    {
        return (bool)preg_match('/^1[345789]\d{9}$/', $value);
    }

    /**
     * 验证邮箱
     *
     * @param  string $email
     * @return bool
     */
    public static function isEmail(string $email): bool
    {
        return (bool)preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/", $email);
    }

    /**
     * 是否字母或数字
     *
     * @param  string $str
     * @return bool
     */
    public static function isAlum(string $str): bool
    {
        return ctype_alnum($str);
    }

    /**
     * 验证是微信内还是微信外
     *
     * @return bool
     */
    public static function isWechat(): bool
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false;
    }

    /**
     * 是否包含特殊字符
     *
     * @param  string $str
     * @return bool
     */
    public static function isSpecialCharacter(string $str): bool
    {
        $res = preg_match('/[\Q~!@#$%^&*()+-_=.:?<>\E]/', $str);
        return (bool)$res;
    }

    /**
     * 验证字符串是否全部是中文
     *
     * @param  string $str
     * @return bool true表示全部是中文，false表示部分是中文或没有中文
     */
    public static function isAllChinese(string $str): bool
    {
        return (bool)preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $str);
    }

    /**
     * 比较两个任意精度的数字是否相等
     *
     * @param  string   $num1
     * @param  string   $num2
     * @param  int|null $scale 允许误差的小数点个数
     * @return bool
     */
    public static function NumericEqual(string $num1, string $num2, ?int $scale = null): bool
    {
        $result = bccomp($num1, $num2, $scale);
        return $result === 0;
    }

    /**
     * 判断是否为手机端
     *
     * @return boolean
     */
    public static function isMobileDevice(): bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $clientKeywords = array('nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp',
                'sie-', 'philips', 'panasonic', 'alcatel', 'meizu', 'android', 'netfront', 'symbian',
                'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'
            );
            if (preg_match("/(" . implode('|', $clientKeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        return false;
    }
}