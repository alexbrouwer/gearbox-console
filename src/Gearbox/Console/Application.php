<?php

namespace Gearbox\Console;

use Gearbox\Console\Exception\RuntimeException;
use SebastianBergmann\Environment\Console;
use Zend\EventManager\EventManagerInterface;
use Zend\ServiceManager\ServiceManager;

class Application
{
    /**
     * @var array
     */
    protected $configuration = [];

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var EventManagerInterface
     */
    protected $events;

    /**
     * @var ConsoleEvent
     */
    protected $event;

    /**
     * @var array
     */
    protected $defaultListeners = [
        'ConsoleListener'
    ];

    /**
     * Application constructor.
     *
     * @param array $configuration
     * @param ServiceManager $serviceManager
     */
    public function __construct(array $configuration, ServiceManager $serviceManager, EventManagerInterface $events = null)
    {
        $this->configuration = $configuration;
        $this->serviceManager = $serviceManager;
        $this->setEventManager($events ? : $serviceManager->get('EventManager'));
    }

    /**
     * Retrieve the service manager
     *
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }

    /**
     * Set the event manager instance
     *
     * @param  EventManagerInterface $eventManager
     *
     * @return Application
     */
    public function setEventManager(EventManagerInterface $eventManager)
    {
        $eventManager->setIdentifiers([
            __CLASS__,
            get_class($this),
        ]);
        $this->events = $eventManager;

        return $this;
    }

    /**
     * Retrieve the event manager
     *
     * Lazy-loads an EventManager instance if none registered.
     *
     * @return EventManagerInterface
     */
    public function getEventManager()
    {
        return $this->events;
    }

    public function bootstrap($listeners)
    {
        $serviceManager = $this->serviceManager;
        $events         = $this->events;
        // Setup default listeners
        $listeners = array_unique(array_merge($this->defaultListeners, $listeners));
        foreach ($listeners as $listener) {
            $serviceManager->get($listener)->attach($events);
        }

        // Setup Console Event
        $this->event = $event  = new ConsoleEvent();
        $event->setName(ConsoleEvent::EVENT_BOOTSTRAP);
        $event->setTarget($this);
        $event->setApplication($this);

        // Trigger bootstrap events
        $events->triggerEvent($event);
        return $this;
    }

    public function run()
    {
        $events = $this->events;
        $event  = $this->event;

        // Define callback used to determine whether or not to short-circuit
        $shortCircuit = function ($r) use ($event) {
            if ($event->getError()) {
                return true;
            }
            return false;
        };


        // Trigger run event
        $event->setName(ConsoleEvent::EVENT_RUN);
        $event->stopPropagation(false); // Clear before triggering
        $events->triggerEventUntil($shortCircuit, $event);

        if ($event->isError()) {
            // Trigger error event
            $event->setName(ConsoleEvent::EVENT_ERROR);
            $event->stopPropagation(false); // Clear before triggering
            $events->triggerEventUntil($shortCircuit, $event);
        }

        if($event->isError() && $event->getError() instanceof \Exception) {
            $error = $event->getError();
            if(!$event->getError() instanceof \Exception) {
                $error = new RuntimeException($error);
            }

            throw $error;
        }

        return $this;
    }

    /**
     * @param array $configuration
     *
     * @return Application
     */
    public static function init(array $configuration)
    {
        // Prepare the service manager
        $smConfig = isset($configuration['service_manager']) ? $configuration['service_manager'] : [];
        $smConfig = new Service\ServiceManagerConfig($smConfig);

        $serviceManager = new ServiceManager();
        $smConfig->configureServiceManager($serviceManager);
        $serviceManager->setService('ApplicationConfig', $configuration);

        // Load modules
        $serviceManager->get('ModuleManager')->loadModules();

        // Prepare list of listeners to bootstrap
        $listenersFromAppConfig = isset($configuration['listeners']) ? $configuration['listeners'] : [];
        $config = $serviceManager->get('config');
        $listenersFromConfigService = isset($config['listeners']) ? $config['listeners'] : [];
        $listeners = array_unique(array_merge($listenersFromConfigService, $listenersFromAppConfig));

        return $serviceManager->get('Application')->bootstrap($listeners);
    }
}
