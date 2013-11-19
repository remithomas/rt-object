<?php

namespace RtObject\RtObject;

use RtObject\RtObject\Format;

class Base{
    
    /**
     * ID of object
     * @var integer 
     */
    protected $_objectId;
    
    /**
     *
     * @var string 
     */
    protected $category;

    /**
     *
     * @var string 
     */
    protected $key;

    /**
     *
     * @var string 
     */
    protected $_format = Format::FORMAT_TEXT;

    /**
     * Value
     * @var string 
     */
    protected $value;
    
    /**
     * Constructor
     */
    public function __construct($key = "", $value = "", $format = Format::FORMAT_TEXT, $category = "", $objectid = null) {
        $this   ->setKey($key)
                ->setValue($value)
                ->setFormat($format)
                ->setCategory($category)
                ->setObjectid($objectid);
    }
    
    /**
     * 
     * @param integer $objectid
     * @return \RtObject\RtObject\Base
     */
    public function setObjectid($objectid){
        $this->_objectId = $objectid;
        return $this;
    }

    

    /**
     * Set format
     * @param string $format
     * @return \RtObject\Entity\Base
     */
    public function setFormat($format = Format::FORMAT_TEXT){
        $this->_format = $format;
        return $this;
    }
    
    /**
     * Get Format
     * @return string
     */
    public function getFormat(){
        return $this->_format;
    }
    
    /**
     * 
     * @param string $value
     * @return \RtObject\RtObject\Base
     */
    public function setValue($value){
        $this->value = $value;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getValue(){
        return $this->value;
    }
    
    /**
     * 
     * @param string $category
     * @return \RtObject\RtObject\Base
     */
    public function setCategory($category){
        $this->category = $category;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getCategory(){
        return $this->category;
    }
    
    /**
     * 
     * @param string $key
     * @return \RtObject\RtObject\Base
     */
    public function setKey($key){
        $this->key = $key;
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getKey(){
        return $this->key;
    }
}