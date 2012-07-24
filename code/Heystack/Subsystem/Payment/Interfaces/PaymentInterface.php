<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Payment\Interfaces;

/**
 * Defines the methods the need to be implemented by the Payment class
 * 
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
interface PaymentInterface 
{
    /**
     * Sets the Transaction ID on the Payment object
     * @param int $transactionID
     */
    public function setTransactionID($transactionID);
    
    /**
     * Returns the Transaction ID from the Payment object
     */
    public function getTransactionID();
    
    /**
     * Sets the Status on the Payment object
     * @param string $status
     */
    public function setStatus($status);
    
    /**
     * Returns the Status of the Payment object
     */
    public function getStatus();
    
    /**
     * Sets the Currency Code on the Payment object
     * @param type $currencyCode
     */
    public function setCurrencyCode($currencyCode);
    
    /**
     * Retrieves the Currency Code of the Payment object
     */
    public function getCurrencyCode();
    
    /**
     * Sets the Message on the Payment object
     * @param string $message
     */
    public function setMessage($message);
    
    /**
     * Returns the Message from the Payment object
     */
    public function getMessage();
    
    /**
     * Sets the Amount of the Payment
     * @param \float $amount
     */
    public function setAmount(\float $amount);
    
    /**
     * Retrieves the Payment's amount
     */
    public function getAmount();
    
    /**
     * Sets the IP of the current user on the Payment object
     * @param string $ip
     */
    public function setIP($ip);
    
    /**
     * Retrieves the IP of the user who made the payment
     */
    public function getIP();
    
    /**
     * Sets the type of transaction on the Payment object
     * @param string $transactionType
     */
    public function setTransactionType($transactionType);
    
    /**
     * Retrieves the Payment's Transaction Type
     */
    public function getTransactionType();
    
    /**
     * Sets the Merchant Reference on the Payment object
     * @param string $merchantReference
     */
    public function setMerchantReference($merchantReference);
    
    /**
     * Retrieves the Merchant reference from the Payment object
     */
    public function getMerchantReference();
}
