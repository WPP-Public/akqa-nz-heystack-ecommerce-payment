<?php

namespace Heystack\Subsystem\Payment\DPS\Traits;

trait DPSPaymentTrait
{
    public function setTransactionReference($transactionReference)
    {
        $this->setField('TransactionReference',$transactionReference);
    }
    
    public function getTransactionReference()
    {
        return $this->record['TransactionReference'];
    }
    
    public function setAuthCode($authCode)
    {
        $this->setField('AuthCode',$authCode);
    }
    
    public function getAuthCode()
    {
        return $this->record['AuthCode'];
    }
}