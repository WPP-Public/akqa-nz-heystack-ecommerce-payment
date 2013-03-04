<?php
use Camspiers\DependencyInjection\SharedContainerFactory;

SharedContainerFactory::addExtension(new Heystack\Subsystem\Payment\DependencyInjection\ContainerExtension);
SharedContainerFactory::addExtension(new Heystack\Subsystem\Payment\DPS\DependencyInjection\ContainerExtension);