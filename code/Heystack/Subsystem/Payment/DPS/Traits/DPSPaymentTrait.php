<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Traits namespace
 */
namespace Heystack\Subsystem\Payment\DPS\Traits;

/**
 * Provides an implementation for the DPSPaymentInterface for use on a Payment object
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
trait DPSPaymentTrait
{
    /**
     * Sets the TransactionReference on the Payment object
     * @param string $transactionReference
     */
    public function setTransactionReference($transactionReference)
    {
        $this->setField('TransactionReference',$transactionReference);
    }

    /**
     * Returns the Transaction Reference from the Payment object
     */
    public function getTransactionReference()
    {
        return $this->record['TransactionReference'];
    }

    /**
     * Sets the AuthCode on the Payment object
     * @param string $authCode
     */
    public function setAuthCode($authCode)
    {
        $this->setField('AuthCode',$authCode);
    }

    /**
     * Returns the AuthCode from the Payment object
     */
    public function getAuthCode()
    {
        return $this->record['AuthCode'];
    }
}
