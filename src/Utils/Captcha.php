<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * Captcha
 * @package NspTeam\Component\Tools\Utils
 */
class Captcha
{
    /**
     * 获取自定义验证码图片
     *
     * @param string $string 需要显示的字符串
     * @return false|string
     * @throws \Exception
     */
    public static function img(string $string)
    {
        $length = strlen($string);
        // 先定义图片的长、宽
        $img_height = 75 + random_int(1, 3) * $length;
        $img_width = 30;
        // 新建一个真彩色图像, 背景黑色图像
        $resourceImg = imagecreatetruecolor($img_height, $img_width);
        // 文字颜色
        $text_color = imagecolorallocate($resourceImg, 255, 255, 255);
        for ($i = 0; $i < $length; $i++) {
            $font = random_int(5, 6);
            $x = $i * $img_height / $length + random_int(1, 3);
            $y = random_int(1, 10);
            // 写入字符
            imagestring($resourceImg, $font, $x, $y, $string[$i], $text_color);
        }
        ob_start();
        // 生成png格式
        ImagePNG($resourceImg);
        $data = ob_get_clean();
        ImageDestroy($resourceImg);

        return $data;
    }
}