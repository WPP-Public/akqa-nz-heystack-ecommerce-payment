<?php

use Camspiers\DependencyInjection\SharedContainerFactory;
use Heystack\Payment;

SharedContainerFactory::addExtension(new Payment\DependencyInjection\ContainerExtension());
SharedContainerFactory::addExtension(new Payment\DPS\DependencyInjection\ContainerExtension());
