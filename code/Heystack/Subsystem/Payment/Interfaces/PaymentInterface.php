<?php

namespace Heystack\Subsystem\Payment\Interfaces;

interface PaymentInterface 
{
    public function setTransactionID($transactionID);
    public function getTransactionID();
    
    public function setStatus($status);
    public function getStatus();
    
    public function setCurrencyCode($currencyCode);
    public function getCurrencyCode();
    
    public function setMessage($message);
    public function getMessage();
    
    public function setAmount(\float $amount);
    public function getAmount();
    
    public function setIP($ip);
    public function getIP();
    
    public function setTransactionType($transactionType);
    public function getTransactionType();
    
    public function setMerchantReference($merchantReference);
    public function getMerchantReference();
}
