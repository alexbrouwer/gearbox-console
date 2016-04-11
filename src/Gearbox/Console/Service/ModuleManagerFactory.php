<?php

namespace Gearbox\Console\Service;

use Interop\Container\ContainerInterface;
use Zend\ModuleManager\Listener\DefaultListenerAggregate;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\ModuleEvent;
use Zend\ModuleManager\ModuleManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ModuleManagerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configuration = $container->get('ApplicationConfig');
        $listenerOptions = new ListenerOptions($configuration['module_listener_options']);
        $defaultListeners = new DefaultListenerAggregate($listenerOptions);
        $serviceListener = $container->get('ServiceListener');

        $serviceListener->addServiceManager(
            $container,
            'service_manager',
            'Zend\ModuleManager\Feature\ServiceProviderInterface',
            'getServiceConfig'
        );
        $serviceListener->addServiceManager(
            'ValidatorManager',
            'validators',
            'Zend\ModuleManager\Feature\ValidatorProviderInterface',
            'getValidatorConfig'
        );
        $serviceListener->addServiceManager(
            'FilterManager',
            'filters',
            'Zend\ModuleManager\Feature\FilterProviderInterface',
            'getFilterConfig'
        );
        $serviceListener->addServiceManager(
            'HydratorManager',
            'hydrators',
            'Zend\ModuleManager\Feature\HydratorProviderInterface',
            'getHydratorConfig'
        );

        $events = $container->get('EventManager');
        $defaultListeners->attach($events);
        $serviceListener->attach($events);

        $moduleEvent = new ModuleEvent();
        $moduleEvent->setParam('ServiceManager', $container);

        $moduleManager = new ModuleManager($configuration['modules'], $events);
        $moduleManager->setEvent($moduleEvent);

        return $moduleManager;
    }
}
