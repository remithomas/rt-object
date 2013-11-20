<?php

namespace RtObject\RtObject;

class Format{
    
    const FORMAT_JSON = "json";
    const FORMAT_TEXT = "text";
    const FORMAT_INTEGER = "integer";
    const FORMAT_DATETIME = "datetime";
    const FORMAT_BOOLEAN = "boolean";
    
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
            self::FORMAT_BOOLEAN,
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
            case self::FORMAT_BOOLEAN:
                return ($value == "true") ;
                
            default : 
                return $value;
        }
    }

    /**
     * Encode the value for the DB
     * @param multiple $value
     * @param string $format
     * @return string
     */
    public static function encodeValue($value, $format = self::FORMAT_TEXT){
        // value
        switch($format){
            case self::FORMAT_TEXT:
                return $value;
            case self::FORMAT_JSON:
                if(!is_string($value)){
                    return \Zend\Json\Json::encode($value);
                }
                return $value;
            case self::FORMAT_INTEGER:
                return (integer) $value;
            case self::FORMAT_BOOLEAN:
                if($value == "true" || $value == true || $value == 1 || $value == "1"){
                    return "true";
                }else{
                    return "false";
                }
                
            default :
                return $value;
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
