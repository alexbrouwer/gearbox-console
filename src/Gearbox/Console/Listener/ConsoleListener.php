<?php

namespace Gearbox\Console\Listener;

use Gearbox\Console\ConsoleEvent;
use Symfony\Component\Console\Application;
use Zend\EventManager\AbstractListenerAggregate;
use Zend\EventManager\EventManagerInterface;

class ConsoleListener extends AbstractListenerAggregate
{
    /**
     * Attach listeners to an event manager
     *
     * @param  EventManagerInterface $events
     * @param  int $priority
     *
     * @return void
     */
    public function attach(EventManagerInterface $events, $priority = 1)
    {
        $this->listeners[] = $events->attach(ConsoleEvent::EVENT_RUN, [$this, 'onRun']);
    }

    /**
     * @param ConsoleEvent $e
     *
     * @return Application
     */
    protected function getApp(ConsoleEvent $e) {
        $application = $e->getApplication();
        $sm = $application->getServiceManager();

        return $sm->get('ConsoleApplication');
    }

    public function onRun(ConsoleEvent $evt)
    {
        $console = $this->getApp($evt);
        try {
            $console->run();
        } catch(\Exception $e) {
            $evt->setError($e);
        }
    }
}
