<?php

namespace NspTeam\Component\Tools\Wx\MiniProgram\Request;


use NspTeam\Component\Tools\Utils\CurlUtil;

class Ocr
{
    /**
     * @var string|null
     */
    protected $accessToken = null;

    /**
     * @var mixed|null
     */
    protected $params = null;

    protected const DOMAIN = 'https://api.weixin.qq.com';
    protected const BANKCARD= self::DOMAIN.'/cv/ocr/bankcard?type=MODE&img_url=ENCODE_URL&access_token=ACCESS_TOKEN';
    protected const IDCARD= self::DOMAIN.'/cv/ocr/idcard?type=MODE&img_url=ENCODE_URL&access_token=ACCESS_TOKEN';

    /**
     * @param string $accessToken
     * @param mixed|null $params
     */
    public function __construct(string $accessToken, $params=null)
    {
        $this->accessToken = $accessToken;
        $this->params = $params;
    }

    /**
     * @param string $imgUrl
     * @return bool|string
     * @throws \JsonException
     */
    public function idcard(string $imgUrl):string
    {
        $url = str_replace(['ENCODE_URL', 'ACCESS_TOKEN'], [$imgUrl, $this->accessToken], self::IDCARD);
        return CurlUtil::post($url, [], ['Content-Type: application/json']);
    }

    /**
     *
     * @param string $imgUrl
     * @return string
     * @throws \JsonException
     */
    public function bankcard(string $imgUrl):string
    {
        $url = str_replace(['ENCODE_URL', 'ACCESS_TOKEN'], [$imgUrl, $this->accessToken], self::BANKCARD);
        return CurlUtil::post($url, [], ['Content-Type: application/json']);
    }
}