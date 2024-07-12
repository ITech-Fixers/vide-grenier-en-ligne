<?php

/**
 * Front controller
 *
 * PHP version 8.3
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';

$kernel = new Core\Kernel();
$kernel->run();
