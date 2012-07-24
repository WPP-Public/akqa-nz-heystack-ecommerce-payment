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
interface PaymentHandlerInterface 
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
    
    /**
     * Saves the data that comes from the payment form submission for later use
     * @param array $data
     */
    public function savePaymentData(array $data);
    
    /**
     * Perform the final step to complete the payment, or hand the user over to the payment provider
     * @param type $transactionID
     */
    public function executePayment($transactionID);
}