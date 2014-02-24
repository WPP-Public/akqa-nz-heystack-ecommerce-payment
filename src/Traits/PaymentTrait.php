<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Traits namespace
 */
namespace Heystack\Payment\Traits;

/**
 * Provides an implementation for the PaymentInterface for use on a Payment object
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @package Ecommerce-Payment
 */
trait PaymentTrait
{
    /**
     * Sets the Transaction ID on the Payment object
     * @param int $transactionID
     */
    public function setTransactionID($transactionID)
    {
        $this->TransactionID = $transactionID;
    }

    /**
     * Returns the Transaction ID from the Payment object
     */
    public function getTransactionID()
    {
        return $this->TransactionID;
    }

    /**
     * Sets the Status on the Payment object
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->Status = $status;
    }

    /**
     * Returns the Status of the Payment object
     */
    public function getStatus()
    {
        return $this->Status;
    }

    /**
     * Sets the Currency Code on the Payment object
     * @param type $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->CurrencyCode = $currencyCode;
    }

    /**
     * Retrieves the Currency Code of the Payment object
     */
    public function getCurrencyCode()
    {
        return $this->CurrencyCode;
    }

    /**
     * Sets the Message on the Payment object
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->Message = $message;
    }

    /**
     * Returns the Message from the Payment object
     */
    public function getMessage()
    {
        return $this->Message;
    }

    /**
     * Sets the Amount of the Payment
     * @param \float $amount
     */
    public function setAmount(\float $amount)
    {
        $this->Amount = $amount;
    }

    /**
     * Retrieves the Payment's amount
     */
    public function getAmount()
    {
        return $this->Amount;
    }

    /**
     * Sets the IP of the current user on the Payment object
     * @param string $ip
     */
    public function setIP($ip)
    {
        $this->IP = $ip;
    }

    /**
     * Retrieves the IP of the user who made the payment
     */
    public function getIP()
    {
        return $this->IP;
    }

    /**
     * Sets the type of transaction on the Payment object
     * @param string $transactionType
     */
    public function setTransactionType($transactionType)
    {
        $this->TransactionType = $transactionType;
    }

    /**
     * Retrieves the Payment's Transaction Type
     */
    public function getTransactionType()
    {
        return $this->TransactionType;
    }

    /**
     * Sets the Merchant Reference on the Payment object
     * @param string $merchantReference
     */
    public function setMerchantReference($merchantReference)
    {
        $this->MerchantReference = $merchantReference;
    }

    /**
     * Retrieves the Merchant reference from the Payment object
     */
    public function getMerchantReference()
    {
        return $this->MerchantReference;
    }
}
