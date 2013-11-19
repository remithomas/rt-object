rt-object 
==========

Create an entity class to use mysql like NOSQL way.

---------------------------------------
# Ask for contributions
Some ideas [to implement](https://github.com/remithomas/rt-object/pulls) to this useful code ? Or ups [some errors appear](https://github.com/remithomas/rt-object/issues) 

---------------------------------------
# Requirements

* [Zend Framework 2](https://github.com/zendframework/zf2) (latest master)
* MySQL server
* [RtExtends](https://github.com/remithomas/rt-extends)

# Installation
---------------------------------------
## How to install ?

### Using composer.json

```json
{
    "name": "zendframework/skeleton-application",
    "description": "Skeleton Application for ZF2",
    "license": "BSD-3-Clause",
    "keywords": [
        "framework",
        "zf2"
    ],
    "minimum-stability": "dev",
    "homepage": "http://framework.zend.com/",
    "require": {
        "php": ">=5.3.3",
        "zendframework/zendframework": "dev-master",
        "remithomas/rt-object": "dev-master"
    }
}
```

### Activate the module :

application.config.php
```php
<?php
return array(
    'modules' => array(
        'Application',
        'RtObject',
    )
);
?>
```

### What's an object ?

An object is just 2 tables into your database:
* object
* object_data

### How to use it

#### Basic
Create a file Application\Entity\MyObject.php into *module/Application/src/Application/Entity*

```php
<?php
namespace Application\Entity\Entity;

use RtObject\RtObject\RtObject;

class MyObject extends RtObject{
    
    public function __construct() {
       parent::__construct("myobject", "myobject_data", "myobjectid");
    }
}

```

#### Install object
```php
$myObject = new \Application\Entity\MyObject();
$myObject->install();

// if you need extra columns to your object, add into your *module/Application/src/Application/Entity/MyObject.php* this code
public function install($extraColumnObject = array(), $extraColumnDataObject = array(), $extraTableObject = '', $extraTableDataObject = '') {
    parent::install(array(
        'extra_one' => "int(11) NOT NULL DEFAULT '1'",
        'extra_two' => "varchar(512) CHARACTER SET utf8 NOT NULL",
    ));
}

// extra columns to object data
public function install($extraColumnObject = array(), $extraColumnDataObject = array(), $extraTableObject = '', $extraTableDataObject = '') {
    parent::install(array(
        'extra_one' => "int(11) NOT NULL DEFAULT '1'",
        'extra_two' => "varchar(512) CHARACTER SET utf8 NOT NULL",
    ),
    array(
        'extra_one' => "int(11) NOT NULL DEFAULT '1'",
        'extra_two' => "varchar(512) CHARACTER SET utf8 NOT NULL",
    ));
}
```

#### Create object
```php
$myObject = new \Application\Entity\MyObject();
$myObject->createObject();
$myObjectId = $myObject->getObjectId();
```

#### Get object
```php
$myObject = new \Application\Entity\MyObject();
$myObject   ->setObjectId(1);
$myObjectInfo = $myObject->getObject();
```

#### Add data to object
```php
$myObject = new \Application\Entity\MyObject();
$myObject   ->setObjectId(1);
            ->insertData("mycategory","mykey", "myvalue", "text");

// using extra column (to object data)
$myObject = new \Application\Entity\MyObject();
$myObject   ->setObjectId(1);
            ->insertData("mycategory","mykey", "myvalue", "text", array("extra_one"=>1234));
```

#### Get object data
```php
$myObject = new \Application\Entity\MyObject();
$myObject   ->setObjectId(1);
$myObjectData = $myObject->getObjectData();

// get some data
$myObjectData = $myObject->getObjectData("mycategory", "mykey");
```
#### Search object from value
```php
$myObject = new \Application\Entity\MyObject();
$aMyObjects = $myObject->searchValue("myvalue","text",100,0);// limit=100 && offset=0
```

#### object is existing
```php
$myObject = new \Application\Entity\MyObject();
$myObject   ->setObjectId(1);
$bMyObject = $myObject->isExisting();
```


### Roadmap

* remove data from category
* some tools/javadoc