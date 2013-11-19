<?php

namespace RtObject\RtObject;

class Format{
    
    const FORMAT_JSON = "json";
    const FORMAT_TEXT = "text";
    const FORMAT_INTEGER = "integer";
    const FORMAT_DATETIME = "datetime";
    
    /**
     * Get all available formats
     * @return array
     */
    public static function getAllFormat(){
        return array(
            self::FORMAT_JSON,
            self::FORMAT_TEXT,
            self::FORMAT_INTEGER,
            self::FORMAT_DATETIME,
        );
    }
    
    /**
     * Format value
     * @param string $value
     * @param string $format
     * @return multiple
     */
    public static function formatValue($value, $format = self::FORMAT_TEXT){
        // value
        switch($format){
            case self::FORMAT_TEXT:
                return $value;
            case self::FORMAT_JSON:
                return \Zend\Json\Json::decode($value);
            case self::FORMAT_INTEGER:
                return (integer) $value;
        }
    }

    /**
     * Check format
     * @param string $format
     * @return boolean
     */
    public static function checkFormat($format){
        if(in_array($format, self::getAllFormat())){
            return true;
        }else{
            return false;
        }
    }
}
