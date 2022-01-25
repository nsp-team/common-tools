<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * Class CurlUtil
 *
 * @package Maoxp\Tool\core\curlUtil
 */
class CurlUtil
{
    public static $responseBody;

    /**
     * get 请求
     *
     * @param  string     $url         地址
     * @param  array|null $params      请求参数
     * @param  array      $headerArray ['Content-Type: application/json']
     * @param  int        $retry       重试次数
     * @return bool|string
     */
    public static function get(string $url, ?array $params, array $headerArray, int $retry = 1)
    {
        $url = str_replace(' ', '+', $url); //对空格进行转义
        $agent = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22';
        $header = array();
        if (!empty($headerArray)) {
            foreach ($headerArray as $key => $value) {
                $header[] = "$key: $value";
            }
        }
        if (!empty($params)) {
            $sep = (false === strpos($url, '?') ? '?' : '&');
            $url .= ($sep . http_build_query($params));
        }

        $curl = curl_init();
        curl_setopt_array(
            $curl, array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $agent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,           // 头文件信息是否作为数据流输出
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,    // 对认证证书来源的检查
            CURLOPT_SSL_VERIFYHOST => 0,    // verify ssl   生产环境中，这个值应该是 2（默认值）
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_HTTPGET => true,
            CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );
        if (!empty($header)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        self::$responseBody = curl_exec($curl);

        $errorCode = curl_errno($curl);
        curl_close($curl);
        if ($errorCode > 0 && $retry > 1) {
            return self::get($url, $params, $headerArray, ++$retry);
        }

        return self::$responseBody;
    }

    /**
     * post 请求
     *
     * @param  string     $url         地址
     * @param  array|null $body        请求体
     * @param  array      $headerArray ['Content-Type: application/json']
     * @param  int        $retry       重试次数
     * @return bool|string
     * @throws \JsonException
     */
    public static function post(string $url, ?array $body = [], array $headerArray = [], int $retry = 1)
    {
        $header = [];
        $formRequest = false;
        $jsonRequest = false;
        if (!empty($headerArray)) {
            foreach ($headerArray as $key => $value) {
                $header[] = "$key: $value";
                if (strpos($value, "application/json") !== false) {
                    $jsonRequest = true;
                } elseif (strpos($value, "x-www-form-urlencoded")) {
                    $formRequest = true;
                }
            }
        }

        $postFields = $body;
        if ($jsonRequest) {
            $postFields = json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }
        $url = str_replace(' ', '+', $url); //对空格进行转义
        $agent = 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22';

        $curl = curl_init();
        curl_setopt_array(
            $curl, array(
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT => $agent,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,           // 头文件信息是否作为数据流输出
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,    // 对认证证书来源的检查
            CURLOPT_SSL_VERIFYHOST => 0,    // verify ssl   生产环境中，这个值应该是 2（默认值）
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_CUSTOMREQUEST => 'POST',
            )
        );
        if (!empty($header)) {
            curl_setopt($curl, CURLINFO_HEADER_OUT, true);    //追踪句柄的请求字符串
            curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        }
        self::$responseBody = curl_exec($curl);

        $errorCode = curl_errno($curl);
        curl_close($curl);
        if ($errorCode > 0 && $retry > 1) {
            return self::post($url, $body, $headerArray, ++$retry);
        }

        return self::$responseBody;
    }
}
