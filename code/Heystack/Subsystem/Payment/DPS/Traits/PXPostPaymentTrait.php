<?php

namespace Heystack\Subsystem\Payment\DPS\Traits;

trait PXPostPaymentTrait
{
    public function setXMLResponse($xmlResponse)
    {
        $this->setField('XMLResponse',$xmlResponse);
    }
    
    public function getXMLResponse()
    {
        return $this->record['XMLResponse'];
    }
    
    public function setBillingID($billingID)
    {
        $this->setField('BillingID',$billingID);
    }
    public function getBillingID()
    {
        return $this->record['BillingID'];
    }
    
    public function setHelpText($helpText)
    {
        $this->setField('HelpText',$helpText);
    }
    
    public function getHelpText()
    {
        return $this->record['HelpText'];
    }
    
    public function setResponseCode($responseCode)
    {
        $this->setField('ResponseCode',$responseCode);
    }
    
    public function getResponseCode()
    {
        return $this->record['ResponseCode'];
    }
    
    public function setSettlementDate($settlementDate)
    {
        $this->setField('SettlementDate',$settlementDate);
    }
    
    public function getSettlementDate()
    {
        return $this->record['SettlementDate'];
    }
}
