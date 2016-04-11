<?php

namespace Gearbox\Console\Service;

use Zend\Validator\ValidatorPluginManager;

class ValidatorManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = ValidatorPluginManager::class;
}
