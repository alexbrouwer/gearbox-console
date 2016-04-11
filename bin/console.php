#!/usr/bin/php
<?php

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Setup autoloading
if (file_exists('vendor/autoload.php')) {
    $loader = include 'vendor/autoload.php';
}

if (!class_exists('Gearbox\Console\Application')) {
    throw new RuntimeException('Unable to load Console application. Run `php composer.phar install`.');
}

// Run the application!
Gearbox\Console\Application::init(require 'config/application.php')->run();
