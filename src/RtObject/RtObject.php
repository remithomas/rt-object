<?php

namespace RtObject\RtObject;


// db
use Zend\Db\TableGateway\Feature,
    Zend\Db\Sql\Sql,
    Zend\Db\Sql\Insert,
    Zend\Db\Adapter\Adapter,
    Zend\Db\ResultSet\ResultSet;

use RtExtends\Db\Sql\DuplicateInsert;

use RtObject\RtObject\Format;

class RtObject{

    /**
     *
     * @var type 
     */
    protected $_adapter;

    /**
     * Name of the main table SQL
     * @var string 
     */
    protected $_tableObject;
            
    /**
     * Name of the data table SQL
     * @var string 
     */
    protected $_tableData;
    
    /**
     *
     * @var string 
     */
    protected $_tableId = "id";

    /**
     * ID of object
     * @var integer 
     */
    protected $_objectId;

    /**
     * 
     * @param string $tableObject
     * @param string $tableData
     */
    public function __construct($tableObject, $tableData, $tableId = "id") {
        $this   ->setTableObject($tableObject)
                ->setTableData($tableData)
                ->setTableId($tableId);
    }
    
    /**
     * 
     * @param string $tableObject
     * @return \RtObject\Entity\RtObject
     */
    public function setTableObject($tableObject){
        $this->_tableObject = $tableObject;
        return $this;
    }
    
    /**
     * 
     * @param string $tableData
     * @return \RtObject\Entity\RtObject
     */
    public function setTableData($tableData){
        $this->_tableData = $tableData;
        return $this;
    }
    
    /**
     * 
     * @param string $tableId
     * @return \RtObject\Entity\RtObject
     */
    public function setTableId($tableId){
        $this->_tableId = $tableId;
        return $this;
    }
    
    /**
     * Get DB adapter
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getAdapter()
    {
       if (!$this->_adapter) {
          $this->_adapter = \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::getStaticAdapter();
       }
       return $this->_adapter;
    }
    
    /**
     * Install the model
     * @param array $extraColumnObject
     * @param array $extraColumnDataObject
     * @param string $extraTableObject
     * @param string $extraTableDataObject
     * @return \RtObject\RtObject\RtObject
     */
    public function install($extraColumnObject = array(), $extraColumnDataObject = array(), $extraTableObject = "", $extraTableDataObject = ""){
        
        // init
        $adapter = $this->getAdapter();
        
        // create main table of the object
            // initial SQL
            $sql = "CREATE TABLE IF NOT EXISTS `".$this->_tableObject."` ( `".$this->_tableId."` INT NOT NULL AUTO_INCREMENT ";
            
            // extra column SQL
            foreach ($extraColumnObject as $columnName => $columnData){
                $sql .= ", `".$columnName."` ".$columnData;
            }
            
            // closing SQL
            $sql .= ", PRIMARY KEY ( `".$this->_tableId."` ) )".$extraTableObject.";";
        
            // execute SQL
            $adapter->query($sql, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
            
        // create data table
            // head SQL
            $sqlDataObject = "
                CREATE TABLE IF NOT EXISTS `".$this->_tableData."` (
                    `".$this->_tableId."` INT NOT NULL,
                    `category` varchar(128) CHARACTER SET utf8 NOT NULL,
                    `key` varchar(128) CHARACTER SET utf8 NOT NULL,
                    `value` mediumtext CHARACTER SET utf8 NOT NULL,
                    `format` varchar(16) CHARACTER SET utf8 NOT NULL ";
             
            // extra column SQL
            foreach ($extraColumnDataObject as $columnName => $columnData){
                $sql .= ", `".$columnName."` ".$columnData;
            }
            
            // closing SQL
            $sqlDataObject .= ",PRIMARY KEY (`".$this->_tableId."`,`category`,`key`))".$extraTableDataObject.";";
            
            $adapter->query($sqlDataObject, \Zend\Db\Adapter\Adapter::QUERY_MODE_EXECUTE);
            
        return $this;
    }
    
    /**
     * 
     * @param array $dataObject
     * @param array $dataDataObject
     * @return \RtObject\RtObject\RtObject
     */
    public function createObject($dataObject = array(), $dataDataObject = array()){
        
        // init
        $adapter = $this->getAdapter();
        
        $sql = new Sql($adapter);
        $insert = $sql->insert($this->_tableObject);
        
        if(count($dataObject)>0){
            $insert->values($dataObject);
        }
        
        $selectString = $sql->getSqlStringForSqlObject($insert);
        $adapter->query($selectString, Adapter::QUERY_MODE_EXECUTE);
        
        // object id
        $this->setObjectId($adapter->getDriver()->getLastGeneratedValue());
        
        if(count($dataDataObject)>0){
            //$this->insertData($key)
        }
        
        return $this;
    }
    
    
    public function insertData($category = "category", $key = 'key', $value = "", $format = Format::FORMAT_TEXT, $extraColumnData = array()){
        // check category and key
        if($key === "" || $category === "" || $key === null || $category === null){
            throw new \Exception(sprintf(
                'Key or Category are wrong. [key] : "%s" ; [category] : "%s"',
                $key,$category
            ));
        }
        
        // check format
        if(!Format::checkFormat($format)){
            throw new \Exception(sprintf(
                'This format "%s" is not allowed',
                $format
            ));
        }
        
        // init
        $adapter = $this->getAdapter();
        
        // values
        $values = array_merge(array(
            $this->_tableId => $this->getObjectId(),
            'category' => $category,
            'key' => $key,
            'value' => Format::encodeValue($value, $format),
            'format' => $format
        ), $extraColumnData);
        
        $sql = new \RtExtends\Db\Sql($adapter, $this->_tableData);
        
        $DuplicateInsert = $sql->duplicateInsert();
        $DuplicateInsert->values($values);
        
        $sqlString = $sql->getSqlStringForSqlObject($DuplicateInsert);
        $adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE);
        return $this;
    }
    
    /**
     * @todo
     */
    public function insertMultipleData($data){
        
    }
    
    /**
     * 
     * @param integer $objectId
     * @return \RtObject\RtObject\RtObject
     */
    public function setObjectId($objectId){
        $this->_objectId = (integer) $objectId;
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getObjectId(){
        return $this->_objectId;
    }

    /**
     * Get object info
     * @return array
     * @throws \Exception
     */
    public function getObject(){
        // checking
        if(is_null($this->_objectId) || $this->_objectId == 0 || $this->_objectId == "0"){
            throw new \Exception(sprintf(
                'The objectid [%s] is not set',
                $this->_tableId
            ));
        }
        
        // init
        $adapter = $this->getAdapter();
        $return = array();
        
        // where
        $where = array(
            $this->_tableId => $this->_objectId,
        );
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $select = $sql->select();
        $select ->where($where);
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        foreach ($results as $row => $rowValue){
            $return[$row] = $rowValue;
        }
        
        return $return[0];
    }

    /**
     * 
     * @param string $category
     * @param string $key
     * @param integer $limit
     * @param integer $offset
     * @return array
     * @throws \Exception
     */
    public function getObjectData($category = null, $key = null, $limit = 300, $offset = 0){
        
        // checking
        if(is_null($this->_objectId)){
            throw new \Exception(sprintf(
                'The objectid [%s] is not set',
                $this->_tableId
            ));
        }
        
        // init
        $adapter = $this->getAdapter();
        
        // where
        $where = array(
            $this->_tableId => $this->_objectId,
        );
        
        if($category != "" && !is_null($category)) $where["category"] = $category;
        if($key != "" && !is_null($key)) $where["key"] = $key;
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableData);
        $select = $sql->select();
        $select ->where($where)
                ->limit($limit)
                ->offset($offset);
         
        $statement = $sql->prepareStatementForSqlObject($select);
        $results = $statement->execute();
        
        $returnArray = array(
            $this->_tableId => $this->_objectId
        );
        
        foreach ($results as $row){
            $category = $row['category'];
            $key = $row['key'];
            $value = utf8_encode($row['value']);
            $returnArray[$category][$key] = Format::formatValue($value, $row['format']);
        }
        
        return $returnArray;
    }
    
    /**
     * 
     * @param array $search
     * @param integer $limit
     * @param integer $offset
     * @param array $order
     * @return array
     * @throws Exception
     */
    public function searchObjects($search = array(), $limit = 100, $offset = 0, $order = array()){
        
        // no search fields
        if(count($search) === 0){
            throw new Exception("No search fields set");
        }
        
        if(count($order)==0){
            $order = array(
            $this->_objectId . ' ASC'  
            );
        }
        
        // init
        $adapter = $this->getAdapter();
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $select = $sql->select();
        $select ->where($search)
                ->columns(array('*'))
                ->limit($limit)
                ->offset($offset);
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        $resultSet->initialize($stmt->execute());
        
        $keyId = $this->_tableId;
        $return  = array();
        
        foreach ($resultSet as $row){
            $return[] = $row->$keyId;
        }
        
        return $return;
    }
    
    /**
     * 
     * @param array $search
     * @param integer $limit
     * @param integer $offset
     * @param array $order
     * @return array
     * @throws Exception
     */
    public function searchFullObjects($search = array(), $limit = 100, $offset = 0, $order = array()){
        
        // no search fields
        if(count($search) === 0){
            throw new Exception("No search fields set");
        }
        
        if(count($order)==0){
            $order = array(
            $this->_objectId . ' ASC'  
            );
        }
        
        // init
        $adapter = $this->getAdapter();
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $select = $sql->select();
        $select ->where($search)
                ->columns(array('*'))
                ->limit($limit)
                ->offset($offset);
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        $resultSet->initialize($stmt->execute());
        
        return $resultSet->toArray();
    }

    /**
     * Search one object
     * @param array $search
     * @return \RtObject\RtObject\RtObject
     * @throws Exception
     */
    public function searchObject($search = array()){
        
        // no search fields
        if(count($search) === 0){
            throw new Exception("No search fields set");
        }
        
        // init
        $adapter = $this->getAdapter();
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $select = $sql->select();
        $select ->where($search)
                ->limit(1);
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        $resultSet->initialize($stmt->execute());
        
        $keyId = $this->_tableId;
        
        foreach ($resultSet as $row){
            $this->setObjectId($row->$keyId);
        }
        
        return $this;
    }
    
    /**
     * 
     * @param type $value
     * @param type $format
     * @param type $limit
     * @param type $offset
     * @param type $category
     * @param type $key
     * @return array
     */
    public function searchValue($value, $format = Format::FORMAT_TEXT, $limit = 30, $offset = 0, $category = "", $key = ""){
        // init
        $adapter = $this->getAdapter();
        $returnArray = array(); 
        
        // where
        $where = new \Zend\Db\Sql\Where();
        $where->like('value', $value);
        
        if($format != "" && !is_null($format)) $where->like("format", $format);
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableData);
        $select = $sql->select();
        $select ->where($where)
                ->limit($limit)
                ->offset($offset);
        
        $stmt = $sql->prepareStatementForSqlObject($select);
        $resultSet = new ResultSet();
        $resultSet->initialize($stmt->execute());
        
        $keyId = $this->_tableId;
        
        foreach ($resultSet as $row){
            $returnArray[$row->$keyId][$row->category][$row->key] = Format::formatValue($row->value, $row->format);
        }
        return $returnArray;
    }
    
    /**
     * Remove object (and data)
     * @return boolean|\RtObject\RtObject\RtObject
     */
    public function removeObject(){
        if($this->_objectId === 0){
            return false;
        }
        
        // remove data
        $this->removeData();
        
        // init remove object
        $adapter = $this->getAdapter();
        
        // where
        $where = array(
            $this->_tableId => $this->_objectId,
        );
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $delete = $sql->delete();
        $delete ->where($where);
        
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        
        return $this;
    }

    public function removeData(){
        if($this->_objectId === 0){
            return false;
        }
        
        // init
        $adapter = $this->getAdapter();
        
        // where
        $where = array(
            $this->_tableId => $this->_objectId,
        );
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableData);
        $delete = $sql->delete();
        $delete ->where($where);
        
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        
        return $this;
    }

    /**
     * Remove data from key
     * @param string $key
     * @return boolean|\RtObject\RtObject\RtObject
     */
    public function removeDataFromKey($key){
        if($this->_objectId === 0){
            return false;
        }
        
        // init
        $adapter = $this->getAdapter();
        
        // where
        $where = array(
            $this->_tableId => $this->_objectId,
            "key" => $key
        );
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableData);
        $delete = $sql->delete();
        $delete ->where($where);
        
        $deleteString = $sql->getSqlStringForSqlObject($delete);
        $adapter->query($deleteString, Adapter::QUERY_MODE_EXECUTE);
        
        return $this;
    }
    
    /**
     * 
     * @param type $data
     * @return boolean|\RtObject\RtObject\RtObject
     */
    public function updateObject($data = array()){
        if($this->_objectId === 0){
            return false;
        }
        
        // init
        $adapter = $this->getAdapter();
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $update = $sql->update();
        $update ->set($data)
                ->where(array($this->_tableId => $this->_objectId));
        
        $updateString = $sql->getSqlStringForSqlObject($update);
        $adapter->query($updateString, Adapter::QUERY_MODE_EXECUTE);
        
        return $this;
    }
    
    /**
     * 
     * @return boolean
     */
    public function isExisting(){
        if(is_null($this->_objectId ) || $this->_objectId == 0 || $this->_objectId == "0"){
            return false;
        }
        
        // init
        $adapter = $this->getAdapter();
        
        $sql = new \Zend\Db\Sql\Sql($adapter, $this->_tableObject);
        $select = $sql->select();
        $select ->columns(array('num' => new \Zend\Db\Sql\Expression('COUNT(*)')))
                ->where(array($this->_tableId => $this->_objectId));
        
        $sqlString = $sql->getSqlStringForSqlObject($select);
        
        return ($adapter->query($sqlString, Adapter::QUERY_MODE_EXECUTE)->current()->num === "1");
        
    }
}