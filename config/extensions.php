<?php
use Camspiers\DependencyInjection\SharedContainerFactory;

SharedContainerFactory::addExtension(new Heystack\Subsystem\Payment\ContainerExtension);
SharedContainerFactory::addExtension(new Heystack\Subsystem\Payment\DPS\ContainerExtension);