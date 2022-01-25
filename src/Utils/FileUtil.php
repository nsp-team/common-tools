<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * FileUtil
 *
 * @package NspTeam\Component\Tools\Utils
 */
class FileUtil
{
    /**
     * 返回父目录的路径，similar to `dirname()`
     *
     * @param  string $path
     * @param  int    $levels
     * @return string
     * @see    https://secure.php.net/manual/en/function.dirname.php
     */
    public static function dirname(string $path, int $levels = 1): string
    {
        $pos = mb_strrpos(str_replace('\\', '/', $path), '/');
        if ($pos === false) {
            return '';
        }

        $path = mb_substr($path, 0, $pos);
        if ($levels <= 1) {
            return $path;
        }

        return static::dirname($path, --$levels);
    }

    /**
     * 获取文件路径的数组信息
     *
     * @param  string $path
     * @return array
     */
    public static function getAttribute(string $path): array
    {
        ['basename' => $basename, 'dirname' => $dirname, 'extension' => $extension, 'filename' => $filename] = pathinfo($path);
        return compact('basename', 'dirname', 'extension', 'filename');
    }

    /**
     * 获取文件名(包含后缀)
     *
     * @param  string $filename
     * @return string
     */
    public static function getFilenameWithExtension(string $filename): string
    {
        ['extension' => $extension, 'filename' => $filename] = self::getAttribute($filename);
        return $filename . '.' . $extension;
    }

    /**
     * 获取文件名(不包含后缀)
     *
     * @param  string $filename
     * @return string
     */
    public static function getFilenameNoExtension(string $filename): string
    {
        ['filename' => $filename] = self::getAttribute($filename);
        return $filename ;
    }

    /**
     * 获取文件后缀
     *
     * @param  string $filename
     * @return string
     */
    public static function getExtension(string $filename): string
    {
        return self::getAttribute($filename)['extension'];
    }

    /**
     * 远端文件下载到本地
     *
     * @param string $url
     * @param string $path
     */
    public static function downloadFile(string $url, string $path): void
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $file = curl_exec($ch);
        curl_close($ch);
        $resource = fopen($path, 'wb');
        fwrite($resource, $file);
        fclose($resource);
    }

    /**
     * 自定义写入日志
     *
     * @param string      $content     日志内容
     * @param string|null $dir         写入地址(绝对地址)
     * @param string|null $logFileName 文件名
     */
    public static function writeLog(string $content, ?string $dir = null, ?string $logFileName = null): void
    {
        $maxFileSize = 10240; // in KB 10240
        if (empty($logFileName)) {
            $logFileName = date("Ymd");
        }

        if (empty($dir)) {
            $dir = "./logs";
        }
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0775, true)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
            }
        }

        try {
            @chmod($dir, 0775);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to change permissions for directory \"$dir\": " . $e->getMessage(), $e->getCode());
        }

        $logFilePath = $dir . "/$logFileName.log";
        if (($fp = fopen($logFilePath, 'ab')) === false) {
            throw new \RuntimeException("Unable to append to log file: {$logFilePath}");
        }
        flock($fp, LOCK_EX);
        clearstatcache();

        if (filesize($logFilePath) > $maxFileSize * 1024) {
            flock($fp, LOCK_UN);
            fclose($fp);
            $newFile = sprintf("%s/%s.log", $dir, $logFileName);
            // 将文件过大size重命名，留存备用日志filename
            if (!rename($logFilePath, $newFile)) {
                throw new \RuntimeException("rename file {$logFilePath} fail");
            }
            // 利用append原子追加方式写日志，同时附加文件锁的形式避免俩次日志间产生穿插
            $writeResult = file_put_contents($logFilePath, date("Y-m-d H:i:s") . "\t" . $content . "\n\n", FILE_APPEND | LOCK_EX);
            if ($writeResult === false) {
                $error = error_get_last();
                throw new \RuntimeException("Unable to export log through file!: {$error['message']}");
            }
            // 写入日志长度是否相同
            $textSize = strlen($content);
            if ($writeResult < $textSize) {
                throw new \RuntimeException("Unable to export whole log through file! Wrote $writeResult out of $textSize bytes.");
            }
        } else {
            $writeResult = fwrite($fp, date("Y-m-d H:i:s") . "\t" . $content . "\n\n");
            if ($writeResult === false) {
                $error = error_get_last();
                throw new \RuntimeException("Unable to export log through file!: {$error['message']}");
            }
            $textSize = strlen($content);
            if ($writeResult < $textSize) {
                throw new \RuntimeException("Unable to export whole log through file! Wrote $writeResult out of $textSize bytes.");
            }
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

}