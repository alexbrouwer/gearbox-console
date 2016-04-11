<?php

namespace Gearbox\Console\Service;

use Gearbox\Console\Listener\ConsoleListener;
use Interop\Container\ContainerInterface;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\Factory\FactoryInterface;

class ConsoleListenerFactory implements FactoryInterface
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
        return new ConsoleListener();
    }
}
