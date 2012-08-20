<?php

define('HEYSTACK_BASE_PATH', dirname(__DIR__));

if (file_exists(HEYSTACK_BASE_PATH . '/vendor/autoload.php')) {

    $loader = require HEYSTACK_BASE_PATH . '/vendor/autoload.php';

} else {

    $loader = require BASE_PATH . '/vendor/autoload.php';

}

$loader->add('Heystack\Subsystem\Payment\Test', __DIR__);

define('UNIT_TESTING', true);

\Director::setBaseURL('http://localhost/');

