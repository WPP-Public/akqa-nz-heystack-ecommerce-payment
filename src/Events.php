<?php
/**
 * This file is part of the Ecommerce-Vouchers package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Payment;

/**
 * Events holds constant references to triggerable dispatch events.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Products
 * @see Symfony\Component\EventDispatcher
 *
 */
final class Events
{
    /**
     * Used to indicate that the payment was successful
     */
    const SUCCESSFUL    = 'payment.successful';

    /**
     * Used to indicate that the payment has failed
     */
    const FAILED        = 'payment.failed';
}
