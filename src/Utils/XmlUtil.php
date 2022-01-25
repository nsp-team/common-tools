<?php
declare(strict_types=1);

namespace NspTeam\Component\Tools\Utils;

use DOMElement;
use DOMException;

/**
 * XmlUtil
 *
 * @package NspTeam\Component\Tools\Utils
 */
class XmlUtil
{
    /**
     * XML转数组
     *
     * @param  string $xml xml
     * @return array
     */
    public static function toArray(string $xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $xmlString = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
        return json_decode(json_encode($xmlString), true);
    }

    /**
     * 数组or对象转xml
     *
     * @param  array|object $data
     * @return string
     */
    public static function toXml($data): string
    {
        if (is_object($data)) {
            $data = get_object_vars($data);
        }
        $xml = '';
        foreach ($data as $key => $val) {
            if (is_null($val)) {
                $xml .= "<$key/>\n";
            } else {
                if (!is_numeric($key)) {
                    $xml .= "<$key>";
                }
                if (is_array($val) || is_object($val)) {
                    $xml .= static::toXml($val);
                } else {
                    $xml .= $val;
                }
                if (!is_numeric($key)) {
                    $xml .= "</$key>";
                }
            }
        }
        return $xml;
    }

    public static function isValidXmlName($name): bool
    {
        try {
            new DOMElement($name);
            return true;
        } catch (DOMException $e) {
            return false;
        }
    }
}