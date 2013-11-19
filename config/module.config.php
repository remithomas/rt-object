<?php
namespace RtObject;
return array(
    'service_manager' => array(
        'aliases' => array(
            'RtObject-ZendDbAdapter' => 'Zend\Db\Adapter\Adapter',
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function ($serviceManager) {
                $adapterFactory = new Zend\Db\Adapter\AdapterServiceFactory();
                $adapter = $adapterFactory->createService($serviceManager);

                \Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);

                return $adapter;
            }
        ),
    ),
);