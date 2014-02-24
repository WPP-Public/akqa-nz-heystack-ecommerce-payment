<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Event namespace
 */
namespace Heystack\Payment\Events;

use Symfony\Component\EventDispatcher\Event;
use Heystack\Payment\Interfaces\PaymentInterface;

/**
 * Payment Event
 *
 * Events dispatched from the CurrencyService will have this object attached
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 *
 */
class PaymentEvent extends Event
{

    protected $payment;

    public function __construct(PaymentInterface $payment)
    {
        $this->payment = $payment;
    }

    public function getPayment()
    {
        return $this->payment;
    }

}
