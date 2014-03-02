<?php

use Heystack\Payment;

return [
    new Payment\DependencyInjection\ContainerExtension(),
    new Payment\DPS\DependencyInjection\ContainerExtension()
];
