<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Payment\Interfaces;

/**
 * Defines the methods the need to be implemented by the PaymentHandler
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
interface PaymentServiceInterface
{
    /**
     * Sets the configuration on the PaymentHandler
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Returns the Configuration data from the PaymentHandler
     */
    public function getConfig();
}
