<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Subsystem\Payment;

/**
 * Holds constants corresponding to the services defined in the services.yml file
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
final class Services
{    
    /**
     * Holds the identfier of the payment handler
     * For use with the ServiceStore::getService($identifier) call
     */
    const PAYMENT_HANDLER = 'payment_handler';
    
    /**
     * Holds the identifier of the payment subscriber
     * For use with the ServiceStore::getService($identifier) call
     */
    const PAYMENT_SUBSCRIBER = 'payment_subscriber';
    
    /**
     * Holds the identifier of the payment input processor
     * For use with the ServiceStore::getService($identifier) call
     */
    const PAYMENT_INPUT_PROCESSOR = 'payment_input_processor';
    
    /**
     * Holds the identifier of the payment output processor
     * For use with the ServiceStore::getService($identifier) call
     */
    const PAYMENT_OUTPUT_PROCESSOR = 'payment_output_processor';
}