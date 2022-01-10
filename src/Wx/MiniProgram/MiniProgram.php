<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Wx\MiniProgram;

use Exception;
use NspTeam\Component\Tools\Utils\CurlUtil;
use NspTeam\Component\Tools\Wx\MiniProgram\Request\Ocr;

/**
 * MinProgram
 * @package NspTeam\Component\Tools\Wx\MiniProgram
 *
 * @method Ocr ocr()
 */
class MiniProgram
{
    protected const DOMAIN = 'https://api.weixin.qq.com';
    protected const CODE_2_SESSION_URL = self::DOMAIN . '/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code';
    protected const ACCESS_TOKEN_URL = self::DOMAIN . '/cgi-bin/token?grant_type=client_credential&appid=APPID&secret=APPSECRET';

    protected $appID = null;
    protected $appSecret = null;
    protected $accessToken = null;
    /**
     * default 7200 second
     * @var int
     */
    protected $expires = 7200;

    /**
     * @param null $accessToken
     */
    public function setAccessToken($accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @param string|null $appID
     * @return MiniProgram
     */
    public function setAppID(?string $appID): self
    {
        $this->appID = $appID;
        return $this;
    }

    /**
     * @param string|null $appSecret
     * @return MiniProgram
     */
    public function setAppSecret(?string $appSecret): self
    {
        $this->appSecret = $appSecret;
        return $this;
    }

    public function __construct(string $appID = null, string $appSecret = null)
    {
        $this->appID = $appID;
        $this->appSecret = $appSecret;
    }

    /**
     * 构建对象
     */
    public static function instance(string $appID = null, string $appSecret = null): self
    {
        return new static($appID, $appSecret);
    }

    /**
     * 解密账号个人信息
     *
     * @param string $sessionKey 会话密钥
     * @param string $encryptedData
     * @param string $iv
     * @return array
     * @throws Exception
     */
    public function wxBizDataCrypt(string $sessionKey, string $encryptedData, string $iv): array
    {
        if (empty($encryptedData)) {
            throw new \RuntimeException('encryptedData 非法');
        }
        if (empty($sessionKey)) {
            throw new \RuntimeException('sessionKey 非法');
        }
        if (strlen($sessionKey) !== 24) {
            throw new \RuntimeException('encodingSessionKey 非法', 41001);
        }
        if (empty($iv)) {
            throw new \RuntimeException('iv 非法');
        }
        if (strlen($iv) !== 24) {
            throw new \RuntimeException('iv 非法');
        }

        $aesKey = base64_decode($sessionKey);
        if ($aesKey === false) {
            throw new \RuntimeException('base64解密失败');
        }
        $aesIV = base64_decode($iv);
        if ($aesIV === false) {
            throw new \RuntimeException('base64解密失败');
        }
        $aesCipher = base64_decode($encryptedData);
        if ($aesCipher === false) {
            throw new \RuntimeException('base64解密失败');
        }

        // 算法为 AES-128-CBC，数据采用PKCS#7填充
        $result = openssl_decrypt($aesCipher, "AES-128-CBC", $aesKey, 1, $aesIV);
        $data = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        if ($data === NULL) {
            throw new \RuntimeException('aes 解密失败');
        }
        if ($data['watermark']['appid'] !== $this->appID) {
            throw new \RuntimeException('aes 解密失败, AppID无法匹配');
        }

        return $data;
    }

    /**
     * 登录凭证校验
     *
     * @param string $code 临时登录凭证
     * @return array
     * @throws Exception
     */
    public function loginVerification(string $code): array
    {
        $url = str_replace(['APPID', 'SECRET', 'JSCODE'], [$this->appID, $this->appSecret, $code], self::CODE_2_SESSION_URL);

        $response = CurlUtil::get($url, null, ['Content-Type: application/json']);
        $body = json_decode($response, false, 512, JSON_THROW_ON_ERROR);
        if (isset($body->errcode) && $body->errcode !== 0) {
            throw new \RuntimeException(sprintf("errCode: %s, errorMsg: %s", $body->errcode, $body->errmsg));
        }

        return array(
            'sessionKey' => $body->session_key,  //会话密钥
            'openid' => $body->openid,       //用户唯一标识
            'unionid' => $body->unionid ?? '',   //用户在开放平台的唯一标识符
        );
    }

    /**
     * 返回AccessToken
     *
     * @return string|null
     * @throws Exception
     */
    public function getAccessToken(): ?string
    {
        if (is_null($this->appID) || is_null($this->appSecret)) {
            throw new \InvalidArgumentException("AppID and appSecret not found!");
        }

        $accessTokenUrl = str_replace(['APPID', 'APPSECRET'], [$this->appID, $this->appSecret], self::ACCESS_TOKEN_URL);

        $response = CurlUtil::get($accessTokenUrl, null, ['Content-Type: application/json']);
        $body = json_decode($response, false, 512, JSON_THROW_ON_ERROR);

        if (isset($body->errcode) && $body->errcode !== 0) {
            throw new \RuntimeException(sprintf("errCode: %s, errorMsg: %s", $body->errcode, $body->errmsg));
        }

        $this->accessToken = $body->access_token;
        $this->expires = $body->expires_in;

        return $this->accessToken;
    }

    /**
     * @param string $method
     * @param mixed $params method of params
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, $params)
    {
        if (is_null($this->accessToken)) {
            $accessToken = $this->getAccessToken();
        } else {
            $accessToken = $this->accessToken;
        }

        $className = ucfirst($method);
        $reqClass = "NspTeam\\Component\\Tools\\Wx\\MiniProgram\\Request\\$className";
        if (class_exists($reqClass)) {
            return new $reqClass($accessToken, ...$params);
        }
        throw new \InvalidArgumentException("Api {$method} not found!");
    }
}