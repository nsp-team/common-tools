<?php

namespace NspTeam\Component\Tools\Utils;

class CryptoUtil
{
    /**
     * 加密, utf8编码，输出base64
     *
     * @see http://tool.chacuo.net/cryptdes
     * @param string $input
     * @param string $key 密钥
     * @param string $cipher
     * @param string|null $iv 偏移量, ecb模式不用填写
     * @return array
     */
    public static function desEncrypt(string $input, string $key, string $cipher = 'DES-ECB', ?string $iv = null): array
    {
        if ($cipher === 'DES-ECB') {
            $iv = '';
        } else {
            $ivLength = openssl_cipher_iv_length($cipher);
            $iv = str_repeat('0', $ivLength);
            // 与cipher预期字节长度不匹配
            if ($ivLength !== strlen($iv)) {
                throw new \InvalidArgumentException('IV passed is ' . strlen($iv) . ' bytes long which is longer than the ' . $ivLength . ' expected by selected cipher');
            }
        }

        return [
            'cipher' => $cipher,
            'iv' => $iv,
            // 数据采用PKCS#7填充
            'data' => base64_encode(openssl_encrypt($input, $cipher, $key, OPENSSL_RAW_DATA, $iv))
        ];
    }

    /**
     * 解密
     *
     * @param string $input
     * @param string $key
     * @param string $cipher
     * @param string|null $iv
     * @return string
     */
    public static function desDecrypt(string $input, string $key, string $cipher = 'DES-ECB', ?string $iv = null): string
    {
        if ($cipher === 'DES-ECB') {
            $iv = '';
        } else {
            if (empty($iv)) {
                throw new \InvalidArgumentException('iv params is err');
            }
            $length = openssl_cipher_iv_length($cipher);
            // 与cipher预期字节长度不匹配
            if ($length !== strlen($iv)) {
                throw new \InvalidArgumentException('IV passed is ' . strlen($iv) . ' bytes long which is longer than the ' . $length . ' expected by selected cipher');
            }
        }

        // 数据采用PKCS#7填充
        return openssl_decrypt(base64_decode($input), $cipher, $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * [encrypt aes加密]
     *
     * @param string $input
     * @param string $key
     * @param string $cipher
     * @param string|null $iv
     * @return array
     */
    public static function aesEncrypt(string $input, string $key, string $cipher = 'AES-128-CBC', ?string $iv = null): array
    {
        $ivLength = openssl_cipher_iv_length($cipher);
        if (is_null($iv)) {
            $iv = str_repeat('0', $ivLength);
        } else if ($ivLength !== strlen($iv)) {
            throw new \InvalidArgumentException('IV passed is ' . strlen($iv) . ' bytes long which is longer than the ' . $ivLength . ' expected by selected cipher');
        }

        return [
            'cipher' => $cipher,
            'iv' => $iv,
            'data' => base64_encode(openssl_encrypt($input, $cipher, $key, OPENSSL_RAW_DATA, $iv))
        ];
    }

    /**
     * [decrypt aes解密]
     *
     * @param string $input
     * @param string $key
     * @param string $cipher
     * @param string|null $iv
     * @return false|string
     */
    public static function aesDecrypt(string $input, string $key, string $cipher, ?string $iv = null)
    {
        $decrypted = openssl_decrypt(base64_decode($input), $cipher, $key, OPENSSL_RAW_DATA, $iv);
        return $decrypted;
    }
}