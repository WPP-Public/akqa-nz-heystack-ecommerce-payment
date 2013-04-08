<?php

define('HEYSTACK_BASE_PATH', dirname(__DIR__));
define('BASE_PATH', realpath(HEYSTACK_BASE_PATH));

if (file_exists(HEYSTACK_BASE_PATH . '/vendor/autoload.php')) {

    $loader = require HEYSTACK_BASE_PATH . '/vendor/autoload.php';

} else {

    $loader = require BASE_PATH . '/vendor/autoload.php';

}

use Symfony\Component\ClassLoader\ClassMapGenerator;

$loader->addClassMap(ClassMapGenerator::createMap(HEYSTACK_BASE_PATH . '/sapphire'));
$loader->add('Heystack\Subsystem\Payment\Test', __DIR__);
$loader->add('Heystack\Subsystem\Core\Test', HEYSTACK_BASE_PATH . '/heystack/tests');

define('UNIT_TESTING', true);

\Director::setBaseURL('http://localhost/');

