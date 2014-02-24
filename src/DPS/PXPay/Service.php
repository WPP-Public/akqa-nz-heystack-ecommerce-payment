<?php

/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */
/**
 * Heystack\Payment\DPS namespace
 */

namespace Heystack\Payment\DPS\PXPay;

use Heystack\Payment\Interfaces\PaymentServiceInterface;

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
class Service implements PaymentServiceInterface
{

    public function __construct()
    {

        throw new \Exception('PXPay is not yet implemented');
    }

    public function executePayment($transactionID)
    {

        throw new \Exception('PXPay is not yet implemented');

    }

    public function getConfig()
    {

        throw new \Exception('PXPay is not yet implemented');

    }

    public function savePaymentData(array $data)
    {

        throw new \Exception('PXPay is not yet implemented');

    }

    public function setConfig(array $config)
    {

        throw new \Exception('PXPay is not yet implemented');

    }

}
