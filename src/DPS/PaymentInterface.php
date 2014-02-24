<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Payment\DPS;

use \Heystack\Payment\Interfaces\PaymentInterface as DefaultPaymentInterface;

/**
 * Defines methods that need to be implemented by DPSPayments
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
interface PaymentInterface extends DefaultPaymentInterface
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
