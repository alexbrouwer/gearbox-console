<?php

namespace Gearbox\Console\Service;

use Gearbox\Console\Application;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class ApplicationFactory implements FactoryInterface
{
    /**
     * Create an Application instance
     *
     * @param  ContainerInterface $container
     * @param  string $name
     * @param  null|array $options
     *
     * @return Application
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $configuration = $container->get('ApplicationConfig');

        return new Application($configuration, $container, $container->get('EventManager'));
    }
}
