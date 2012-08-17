<?php

/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */
/**
 * Heystack\Subsystem\Payment\DPS namespace
 */

namespace Heystack\Subsystem\Payment\DPS\PXPay;

use Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface;

/**
 * PXPayHandler allows for payments to be handled through the PXPay interface
 * for DPS payments.
 *
 * @todo Finish this, it is currently in a non-working state.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Heystack
 *
 */
class PXPayHandler implements PaymentServiceInterface
{

    function __construct()
    {
        
        throw new \Exception('PXPay is not yet implemented');
        
    }

}
