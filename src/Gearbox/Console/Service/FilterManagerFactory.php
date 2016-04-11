<?php

namespace Gearbox\Console\Service;

use Zend\Filter\FilterPluginManager;

class FilterManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = FilterPluginManager::class;
}
