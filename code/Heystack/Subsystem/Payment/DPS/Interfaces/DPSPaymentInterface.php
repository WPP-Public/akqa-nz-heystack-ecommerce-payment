<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Payment\DPS\Interfaces;

use \Heystack\Subsystem\Payment\Interfaces\PaymentInterface;

/**
 * Defines methods that need to be implemented by DPSPayments
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
interface DPSPaymentInterface extends PaymentInterface
{
    /**
     * Sets the TransactionReference on the Payment object
     * @param string $transactionReference
     */
    public function setTransactionReference($transactionReference);

    /**
     * Returns the Transaction Reference from the Payment object
     */
    public function getTransactionReference();

    /**
     * Sets the AuthCode on the Payment object
     * @param string $authCode
     */
    public function setAuthCode($authCode);

    /**
     * Returns the AuthCode from the Payment object
     */
    public function getAuthCode();

}
