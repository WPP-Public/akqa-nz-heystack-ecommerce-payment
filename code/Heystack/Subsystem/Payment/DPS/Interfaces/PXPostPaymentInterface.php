<?php

namespace Heystack\Subsystem\Payment\DPS\Interfaces;

interface PXPostPaymentInterface extends DPSPaymentInterface
{
    public function setXMLResponse($xmlResponse);
    public function getXMLResponse();
    
    public function setBillingID($billingID);
    public function getBillingID();
    
    public function setHelpText($helpText);
    public function getHelpText();
    
    public function setResponseCode($responseCode);
    public function getResponseCode();
    
    public function setSettlementDate($settlementDate);
    public function getSettlementDate();
}
