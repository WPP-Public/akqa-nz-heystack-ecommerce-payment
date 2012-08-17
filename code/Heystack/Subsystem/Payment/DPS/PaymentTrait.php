<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Traits namespace
 */
namespace Heystack\Subsystem\Payment\DPS;

/**
 * Provides an implementation for the Heystack\Subsystem\Payment\DPS\PaymentInterface for use on a Payment object
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @package Ecommerce-Payment
 */
trait PaymentTrait
{
    /**
     * Sets the TransactionReference on the Payment object
     * @param string $transactionReference
     */
    public function setTransactionReference($transactionReference)
    {
        $this->TransactionReference = $transactionReference;
    }

    /**
     * Returns the Transaction Reference from the Payment object
     */
    public function getTransactionReference()
    {
        return $this->TransactionReference;
    }

    /**
     * Sets the AuthCode on the Payment object
     * @param string $authCode
     */
    public function setAuthCode($authCode)
    {
        $this->AuthCode = $authCode;
    }

    /**
     * Returns the AuthCode from the Payment object
     */
    public function getAuthCode()
    {
        return $this->AuthCode;
    }
}
