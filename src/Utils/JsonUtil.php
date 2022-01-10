<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

/**
 * JsonUtil
 * @package NspTeam\Component\Tools\Utils
 * @since 0.0.1
 */
class JsonUtil
{
    /**
     * List of JSON Error messages assigned to constant names for better handling of version differences.
     * @var array
     * @since 2.0.7
     */
    public static $jsonErrorMessages = [
        'JSON_ERROR_DEPTH' => 'The maximum stack depth has been exceeded.',
        'JSON_ERROR_STATE_MISMATCH' => 'Invalid or malformed JSON.',
        'JSON_ERROR_CTRL_CHAR' => 'Control character error, possibly incorrectly encoded.',
        'JSON_ERROR_SYNTAX' => 'Syntax error.',
        'JSON_ERROR_UTF8' => 'Malformed UTF-8 characters, possibly incorrectly encoded.', // PHP 5.3.3
        'JSON_ERROR_RECURSION' => 'One or more recursive references in the value to be encoded.', // PHP 5.5.0
        'JSON_ERROR_INF_OR_NAN' => 'One or more NAN or INF values in the value to be encoded', // PHP 5.5.0
        'JSON_ERROR_UNSUPPORTED_TYPE' => 'A value of a type that cannot be encoded was given', // PHP 5.5.0
    ];

    /**
     * Pre-processes the data before sending it to `json_encode()`.
     * @param mixed $data the data to be processed
     * @param array $expressions collection of JavaScript expressions
     * @param string $expPrefix a prefix internally used to handle JS expressions
     * @return mixed the processed data
     */
    protected static function processData($data, array &$expressions, string $expPrefix)
    {
        if (is_object($data)) {
            if ($data instanceof \JsonSerializable) {
                return static::processData($data->jsonSerialize(), $expressions, $expPrefix);
            }
            if ($data instanceof \DateTimeInterface) {
                return static::processData((array)$data, $expressions, $expPrefix);
            }

            if ($data instanceof \SimpleXMLElement) {
                $data = (array) $data;
            } else {
                $result = [];
                foreach ($data as $name => $value) {
                    $result[$name] = $value;
                }
                $data = $result;
            }

            if ($data === []) {
                return new \stdClass();
            }
        }

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $data[$key] = static::processData($value, $expressions, $expPrefix);
                }
            }
        }

        return $data;
    }

    /**
     * Encodes the given value into a JSON string.
     *
     * The method enhances `json_encode()` by supporting JavaScript expressions.
     * In particular, the method will not encode a JavaScript expression that is
     * represented in terms of a [[JsExpression]] object.
     *
     * Note that data encoded as JSON must be UTF-8 encoded according to the JSON specification.
     * You must ensure strings passed to this method have proper encoding before passing them.
     *
     * @param mixed $value the data to be encoded.
     * @param int $options the encoding options. For more details please refer to
     * <https://secure.php.net/manual/en/function.json-encode.php>. Default is `JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE`.
     * @return string the encoding result.
     * @throws \Exception if there is any encoding error.
     */
    public static function encode($value, int $options = 320): string
    {
        $expressions = [];
        $value = static::processData($value, $expressions, uniqid('', true));
        set_error_handler(function () {
            static::handleJsonError(JSON_ERROR_SYNTAX);
        }, E_WARNING);
        $json = json_encode($value, $options);
        restore_error_handler();
        static::handleJsonError(json_last_error());

        return $expressions === [] ? $json : strtr($json, $expressions);
    }

    /**
     * decode json string成php的数据结构
     * @param string $json
     * @param bool $asArray
     * @return mixed|null  如果 json无法被解码， 或者编码数据深度超过了递归限制的话，将会返回null 。
     * @throws \Exception
     */
    public static function decode(string $json, bool $asArray = true)
    {
        if (is_array($json)) {
            throw new \Exception('Invalid JSON data.');
        } elseif (StrUtil::isEmpty($json)) {
            return null;
        }
        $decode = json_decode($json, $asArray, 512, 0);
        static::handleJsonError(json_last_error());
        return $decode;
    }

    /**
     * Handles [[encode()]] and [[decode()]] errors by throwing exceptions with the respective error message.
     * @param int $lastError error code from [json_last_error()]
     * @link https://secure.php.net/manual/en/function.json-last-error.php.
     * @throws \RuntimeException if there is any encoding/decoding error.
     */
    protected static function handleJsonError(int $lastError)
    {
        if ($lastError === JSON_ERROR_NONE) {
            return;
        }
        $availableErrors = [];
        foreach (static::$jsonErrorMessages as $const => $message) {
            if (defined($const)) {
                $availableErrors[constant($const)] = $message;
            }
        }
        if (isset($availableErrors[$lastError])) {
            throw new \RuntimeException($availableErrors[$lastError], $lastError);
        }

        throw new \RuntimeException('Unknown JSON encoding/decoding error.');
    }
}