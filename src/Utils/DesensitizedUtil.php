<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * DesensitizedUtil
 * @package NspTeam\Component\Tools\Utils
 * @since 0.0.1
 */
class DesensitizedUtil
{
    /**
     * 【手机号码】前三位，后4位，其他隐藏，比如135****2210
     *
     * @param string $mobile
     * @return string
     */
    public static function mobilePhone(string $mobile): string
    {
        return preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2', $mobile);
    }

    /**
     * 【身份证号】前1位 和后2位
     *
     * @param string $idCardNum
     * @param int $front 保留：前面的front位数；从1开始
     * @param int $end 保留：后面的end位数；从1开始
     * @return string 脱敏后的身份证
     */
    public static function idCardNum(string $idCardNum, int $front, int $end): string
    {
        if (empty($idCardNum)) {
            return '';
        }

        //需要截取的长度不能大于身份证号长度
        if (($front + $end) > strlen($idCardNum)) {
            return '';
        }
        //需要截取的不能小于0
        if ($front < 0 || $end < 0) {
            return '';
        }

        return StrUtil::hide($idCardNum, $front, $end);
    }

    /**
     * 【密码】密码的全部字符都用*代替，比如：******
     *
     * @param string $password
     * @return string
     */
    public static function password(string $password): string
    {
        if (empty($password)) {
            return '';
        }
        return str_repeat('*', mb_strlen($password));
    }

    /**
     * 银行卡号脱敏
     * eg: 1101 **** **** **** 3256
     *
     * @param string $bankCardNo 银行卡号
     * @return string 脱敏之后的银行卡号
     */
    public static function bankCard(string $bankCardNo): string
    {
        if (empty($bankCardNo)) {
            return '';
        }
        $bankCardNo = str_replace(' ', '', $bankCardNo);
        if (mb_strlen($bankCardNo) < 9) {
            return  $bankCardNo;
        }

        $length = StrUtil::length($bankCardNo);
        $midLength = $length - 8;

        $str = StrUtil::make();
        $str = $str->append($bankCardNo, 0, 4);
        for ($i= 0; $i < $midLength; $i++) {
            if ($i%4 === 0) {
                $str->append(StrUtil::SPACE);
            }
            $str->append('*');
        }
        $str->append(StrUtil::SPACE);
        $str->append($bankCardNo, $length-4 ,$length);

        return  $str->toString();
    }
}