<?php

namespace NspTeam\Component\Tools\Utils;

class CryptoUtil
{
    /**
     * 加密, utf8编码，输出base64
     *
     * @see    http://tool.chacuo.net/cryptdes
     * @param  string      $input
     * @param  string      $key    密钥
     * @param  string      $cipher
     * @param  int         $option
     * @param  string|null $iv     偏移量,
     *                             ecb模式不用填写
     * @return array
     */
    public static function desEncrypt(string $input, string $key, string $cipher = 'DES-ECB', int $option = OPENSSL_RAW_DATA, ?string $iv = null): array
    {
        $iv = self::getIv($cipher, $iv);

        return [
            'cipher' => $cipher,
            'iv' => $iv,
            'key' => $key,
            // 数据采用PKCS#7填充
            'data' => base64_encode(openssl_encrypt($input, $cipher, $key, $option, $iv))
        ];
    }

    /**
     * 解密
     *
     * @param  string      $input
     * @param  string      $key
     * @param  string      $cipher
     * @param  int         $option
     * @param  string|null $iv
     * @return string
     */
    public static function desDecrypt(string $input, string $key, string $cipher = 'DES-ECB', int $option = OPENSSL_RAW_DATA, ?string $iv = null): string
    {
        $iv = self::getIv($cipher, $iv);

        // 数据采用PKCS#7填充
        return openssl_decrypt(base64_decode($input), $cipher, $key, $option, $iv);
    }

    /**
     * aes加密
     * OPENSSL_RAW_DATA 等同于JAVA中填充方式为：Pkcs7-Pkcs5Padding模式填充数据补位
     * OPENSSL_NO_PADDING 等同于 no Padding填充模式
     *
     * @param  string      $input
     * @param  string      $key
     * @param  string      $cipher
     * @param  int         $option
     * @param  string|null $iv
     * @return array
     */
    public static function aesEncrypt(string $input, string $key, string $cipher = 'AES-128-CBC', int $option = OPENSSL_RAW_DATA, ?string $iv = null): array
    {
        $ivLength = openssl_cipher_iv_length($cipher);
        if (is_null($iv)) {
            $iv = str_repeat('0', $ivLength);
        } else if ($ivLength !== strlen($iv)) {
            throw new \InvalidArgumentException('IV passed is ' . strlen($iv) . ' bytes long which is longer than the ' . $ivLength . ' expected by selected cipher');
        }

        $encrypt = openssl_encrypt($input, $cipher, $key, $option, $iv);

        return [
            'cipher' => $cipher,
            'key' => $key,
            'iv' => $iv,
            'encrypt' => $encrypt,
            'data' => base64_encode($encrypt)
        ];
    }

    /**
     * aes解密
     *
     * @param  string      $input
     * @param  string      $key
     * @param  string      $cipher
     * @param  int         $option OPENSSL_RAW_DATA = Pkcs7-Pkcs5
     * @param  string|null $iv
     * @return false|string
     */
    public static function aesDecrypt(string $input, string $key, string $cipher, int $option = OPENSSL_RAW_DATA, ?string $iv = null)
    {
        $decrypted = openssl_decrypt(base64_decode($input), $cipher, $key, $option, $iv);
        return $decrypted;
    }

    /**
     * @param  string $cipher
     * @param  string $iv
     * @return string
     */
    protected static function getIv(string $cipher, string $iv): string
    {
        if ($cipher === 'DES-ECB') {
            $iv = '';
        } else {
            $ivLength = openssl_cipher_iv_length($cipher);
            if ($ivLength !== 0) {
                $iv = $iv ?? str_repeat('0', $ivLength);
            }
            // 与cipher预期字节长度不匹配
            if ($ivLength !== strlen($iv)) {
                throw new \InvalidArgumentException('IV passed is ' . strlen($iv) . ' bytes long which is longer than the ' . $ivLength . ' expected by selected cipher');
            }
        }
        return $iv;
    }
}