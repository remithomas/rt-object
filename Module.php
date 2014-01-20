<?php

namespace RtObject;

use Zend\EventManager\Event,
    Zend\EventManager\StaticEventManager,
    Zend\ModuleManager\ModuleManager,
    Zend\Mvc\MvcEvent;

use Zend\Db\TableGateway\Feature;

class Module
{
    
    /**
     * onBootstrap
     * @param MvcEvent $e
     */
    public function onBootstrap(Event $e){
        
        $application     = $e->getTarget();
        $serviceManager  = $application->getServiceManager();
        $eventManager    = $application->getEventManager();
        
        // set static adapter for all module table gateways
        $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
        Feature\GlobalAdapterFeature::setStaticAdapter($dbAdapter);
        
    }
    
    public function getControllerConfig()
    {
        return array();
    }
    
    public function getServiceConfig() 
    { 
        return array(
        	'factories' => array(
        		'Zend\Db\Adapter\Adapter' => function ($serviceManager) {
                	$adapterFactory = new \Zend\Db\Adapter\AdapterServiceFactory();
                	$adapter = $adapterFactory->createService($serviceManager);

                	\Zend\Db\TableGateway\Feature\GlobalAdapterFeature::setStaticAdapter($adapter);

                	return $adapter;
            	}
        	),
        ); 
    }
    
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
    
    // http://stackoverflow.com/questions/8957274/access-to-module-config-in-zend-framework-2
}
