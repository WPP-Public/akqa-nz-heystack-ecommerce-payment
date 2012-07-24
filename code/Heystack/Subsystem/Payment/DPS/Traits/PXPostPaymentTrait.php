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
 * Provides an implementation for the PXPostPaymentInterface for use on a Payment object
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
trait PXPostPaymentTrait
{
    /**
     * Sets the XMLResponse on the Payment object
     * @param string $xmlResponse
     */
    public function setXMLResponse($xmlResponse)
    {
        $this->setField('XMLResponse',$xmlResponse);
    }
    
    /**
     * Returns the XMLResponse from the Payment object
     */    
    public function getXMLResponse()
    {
        return $this->record['XMLResponse'];
    }
    
    /**
     * Sets the BillingID on the Payment object
     * @param string $billingID
     */    
    public function setBillingID($billingID)
    {
        $this->setField('BillingID',$billingID);
    }
    
    /**
     * Returns the BillingID from the Payment object
     */    
    public function getBillingID()
    {
        return $this->record['BillingID'];
    }
    
    /**
     * Sets the HelpText on the Payment object
     * @param string $helpText
     */    
    public function setHelpText($helpText)
    {
        $this->setField('HelpText',$helpText);
    }
    
    /**
     * Returns the HelpText from the Payment object
     */    
    public function getHelpText()
    {
        return $this->record['HelpText'];
    }
    
    /**
     * Sets the ResponseCode on the Payment object
     * @param string $responseCode
     */    
    public function setResponseCode($responseCode)
    {
        $this->setField('ResponseCode',$responseCode);
    }
    
    /**
     * Returns the ResponseCode from the Payment object
     */    
    public function getResponseCode()
    {
        return $this->record['ResponseCode'];
    }
    
    /**
     * Sets the SettlementDate on the Payment object
     * @param string $settlementDate
     */    
    public function setSettlementDate($settlementDate)
    {
        $this->setField('SettlementDate',$settlementDate);
    }
    
    /**
     * Returns the SettlementDate from the Payment object
     */    
    public function getSettlementDate()
    {
        return $this->record['SettlementDate'];
    }
}
