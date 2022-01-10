<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;


/**
 * StringUtil
 * @package NspTeam\Component\Tools\Utils
 * @since 0.0.1
 */
class StrUtil
{
    protected $character = '';

    /**
     * 字符串常量：空
     */
    public const EMPTY = '';

    /**
     * 字符常量：空格符
     */
    public const SPACE = ' ';
    /**
     * 字符常量：点
     */
    public const DOT = '.';

    public function __construct()
    {
    }

    public static function make(): self
    {
        return new static();
    }

    /**
     * 追加子字符串, 支持从front到end的截取子字符串
     *
     * @param string $appendStr
     * @param int|null $front
     * @param int|null $end
     * @return $this
     */
    public function append(string $appendStr, ?int $front = null, ?int $end = null): self
    {
        if (isset($front)) {
            $length = $end ?? self::length($appendStr);
            $this->character .= mb_substr($appendStr, $front, $length);
        } else {
            $this->character .= $appendStr;
        }
        return $this;
    }

    public function toString(): string
    {
        return $this->character;
    }

    /**
     * 隐藏字符串 前$front位， 后$end位
     *
     * @param string $str
     * @param int $front
     * @param int $end
     * @return string
     */
    public static function hide(string $str, int $front, int $end): string
    {
        $length = mb_strlen($str) - ($front + $end);
        return substr_replace($str, str_repeat('*', $length), $front, $length);
    }

    /**
     * 返回给定字符串中的长度(utf8 汉字占3字节)
     *
     * @param string $string
     * @param bool $byte
     * @return int 字节数
     */
    public static function length(string $string, bool $byte = false): int
    {
        $encoding = $byte ? '8bit' : 'UTF-8';
        return mb_strlen($string, $encoding);
    }

    /**
     * 按字符|字节数来执行，返回由start和length参数指定的字符串部分
     *
     * @param string $str
     * @param int $start
     * @param int|null $length
     * @param bool $byte
     * @return string
     * @see https://www.php.net/manual/zh/function.mb-strcut.php
     */
    public static function substr(string $str, int $start, ?int $length = null, bool $byte = false): string
    {
        if ($length === null) {
            $length = static::length($str, $byte);
        }
        $encoding = $byte ? '8bit' : 'UTF-8';
        return mb_substr($str, $start, $length, $encoding);
    }

    /**
     * 将字符串截断为指定字符长度的子字符串，并启用后缀
     * eg: 指定字符长度...
     *
     * @param string $str
     * @param int $length
     * @param string $suffix
     * @param string|null $encoding
     * @return string
     */
    public static function truncate(string $str, int $length, string $suffix = '...', string $encoding = null): string
    {
        if ($encoding === null) {
            $encoding = mb_internal_encoding();
        }

        if (mb_strlen($str, $encoding) > $length) {
            return rtrim(mb_substr($str, 0, $length, $encoding)) . $suffix;
        }

        return $str;
    }

    /**
     * 检查字符串中是否包含某些字符串
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function contains(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            // (PHP 8)
            if (function_exists('str_contains')) {
                return str_contains($haystack, $needle);
            }
            if ('' !== $needle && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串结尾
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function endsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            // (PHP 8)
            if (function_exists('str_ends_with')) {
                return str_ends_with($haystack, $needle);
            }
            if ((string)$needle === mb_substr($haystack, -static::length($needle))) {
                return true;
            }
        }

        return false;
    }

    /**
     * 检查字符串是否以某些字符串开头
     *
     * @param string $haystack
     * @param string|array $needles
     * @return bool
     */
    public static function startsWith(string $haystack, $needles): bool
    {
        foreach ((array)$needles as $needle) {
            // (PHP 8)
            if (function_exists('str_starts_with')) {
                return str_starts_with($haystack, $needle);
            }
            if ('' !== $needle && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取指定长度的随机字母数字组合的字符串
     *
     * @param int $length
     * @param int|null $type [0: 大小写字母,1: 纯数字,2: 大写字母,3: 中文,4: 大小写字母+纯数字]
     * @param string $addChars
     * @return string
     * @throws \Exception
     */
    public static function randomAlphanumericString(int $length = 6, int $type = null, string $addChars = ''): string
    {
        $str = '';
        switch ($type) {
            case 0:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 1:
                $chars = str_repeat('0123456789', 3);
                break;
            case 2:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
                break;
            case 3:
                $chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
                break;
            case 4:
                $chars = "们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书" . $addChars;
                break;
            default:
                $chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
                break;
        }
        if ($length > 10) {
            $chars = $type === 1 ? str_repeat($chars, $length) : str_repeat($chars, 5);
        }

        if ($type !== 4) {
            $chars = str_shuffle($chars);
            $str = substr($chars, 0, $length);
        } else {
            for ($i = 0; $i < $length; $i++) {
                $randomInt = random_int(0, static::length($chars) - 1);
                $str .= static::substr($chars, $randomInt, 1);
            }
        }
        return $str;
    }

    /**
     * 驼峰转下划线
     *
     * @param string $value
     * @param string $delimiter
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {

        if (!ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = static::lower(preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }

    /**
     * 下划线转驼峰(首字母小写)
     *
     * @param string $value
     * @return string
     */
    public static function camel(string $value): string
    {
        return lcfirst(static::studly($value));
    }

    /**
     * 下划线转驼峰(首字母大写)
     *
     * @param string $value
     * @return string
     */
    public static function studly(string $value): string
    {
        $value = ucwords(str_replace(['-', '_'], ' ', $value));

        return str_replace(' ', '', $value);
    }

    /**
     * 转为首字母大写的标题格式(hello world! => Hello World!)
     *
     * @param string $value
     * @return string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }

    /**
     * 字符串转小写
     *
     * @param string $value
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }

    /**
     * 字符串转大写
     *
     * @param string $value
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }

    /**
     * Encodes string into "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
     *
     * > Note: Base 64 padding `=` may be at the end of the returned string.
     * > `=` is not transparent to URL encoding.
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input the string to encode.
     * @return string encoded string.
     */
    public static function base64UrlEncode(string $input): string
    {
        return strtr(base64_encode($input), '+/', '-_');
    }

    /**
     * Decodes "Base 64 Encoding with URL and Filename Safe Alphabet" (RFC 4648).
     *
     * @see https://tools.ietf.org/html/rfc4648#page-7
     * @param string $input encoded string.
     * @return string decoded string.
     */
    public static function base64UrlDecode(string $input): string
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    /**
     * Counts words in a string.
     * @param string $string
     * @return int
     */
    public static function countWords(string $string): int
    {
        return count(preg_split('/\s+/u', $string, 0, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * 转换一个String字符串为byte数组
     *
     * @param string $string
     * @return array
     */
    public static function getBytes(string $string): array
    {
        $bytes = array();
        for ($i = 0, $iMax = strlen($string); $i < $iMax; $i++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    /**
     * 将字节数组转化为String类型的数据
     *
     * @param array $bytes
     * @return string
     */
    public static function byteToStr(array $bytes): string
    {
        $str = '';
        foreach ($bytes as $ch) {
            $str .= chr($ch);
        }

        return $str;
    }
}