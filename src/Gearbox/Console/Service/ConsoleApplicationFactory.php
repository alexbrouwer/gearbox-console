<?php

namespace Gearbox\Console\Service;

use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConsoleApplicationFactory implements FactoryInterface
{
    /**
     * Create an EventManager instance
     *
     * Creates a new EventManager instance, seeding it with a shared instance
     * of SharedEventManager.
     *
     * @param  ContainerInterface $container
     * @param  string $name
     * @param  null|array $options
     *
     * @return EventManager
     */
    public function __invoke(ContainerInterface $container, $name, array $options = null)
    {
        $application = new Application();
        // TODO configure
//        $application->setCatchExceptions(false);

        return $application;
    }
}
