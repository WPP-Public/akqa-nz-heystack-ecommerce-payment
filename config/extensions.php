<?php

use Camspiers\DependencyInjection\SharedContainerFactory;
use Heystack\Subsystem\Payment;

SharedContainerFactory::addExtension(new Payment\DependencyInjection\ContainerExtension());
SharedContainerFactory::addExtension(new Payment\DPS\DependencyInjection\ContainerExtension());