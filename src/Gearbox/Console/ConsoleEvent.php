<?php

namespace Gearbox\Console;

use Zend\EventManager\Event;

class ConsoleEvent extends Event
{
    const EVENT_BOOTSTRAP = 'bootstrap';
    const EVENT_RUN = 'run';
    const EVENT_ERROR = 'error';

    /**
     * @var Application
     */
    protected $application;

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param Application $application
     *
     * @return $this
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Does the event represent an error response?
     *
     * @return bool
     */
    public function isError()
    {
        return (bool)$this->getParam('error', false);
    }

    /**
     * Set the error message (indicating error in handling request)
     *
     * @param  string $message
     *
     * @return ConsoleEvent
     */
    public function setError($message)
    {
        $this->setParam('error', $message);

        return $this;
    }

    /**
     * Retrieve the error message, if any
     *
     * @return string
     */
    public function getError()
    {
        return $this->getParam('error', '');
    }

}
