<?php

namespace Heystack\Subsystem\Payment\Traits;

trait PaymentTrait
{
    public function setTransactionID($transactionID)
    {
        $this->setField('TransactionID',$transactionID);   
    }
    
    public function getTransactionID()
    {
        return $this->record['TransactionID'];
    }
    
    public function setStatus($status)
    {
        $this->setField('Status',$status);
    }
    
    public function getStatus()
    {
        return $this->record['Status'];
    }
    
    public function setCurrencyCode($currencyCode)
    {
        $this->setField('CurrencyCode',$currencyCode);
    }
    
    public function getCurrencyCode()
    {
        return $this->record['CurrencyCode'];
    }
    
    public function setMessage($message)
    {
        $this->setField('Message',$message);
    }
    
    public function getMessage()
    {
        return $this->record['Message'];
    }
    
    public function setAmount(\float $amount)
    {
        $this->setField('Amount',$amount);
    }
    
    public function getAmount()
    {
        return $this->record['Amount'];
    }
    
    public function setIP($ip)
    {
        $this->setField('IP',$ip);
    }
    
    public function getIP()
    {
        return $this->record['IP'];
    }
    
    public function setTransactionType($transactionType)
    {
        $this->setField('TransactionType',$transactionType);
    }
    
    public function getTransactionType()
    {
        return $this->record['TransactionType'];
    }
    
    public function setMerchantReference($merchantReference)
    {
        $this->setField('MerchantReference',$merchantReference);
    }
    
    public function getMerchantReference()
    {
        return $this->record['MerchantReference'];
    }
}