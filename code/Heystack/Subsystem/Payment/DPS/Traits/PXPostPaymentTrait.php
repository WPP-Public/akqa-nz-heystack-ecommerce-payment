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
 * @author Stevie Mayhew <stevie@heyday.co.nz>
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
        $this->XMLResponse = $xmlResponse;
    }

    /**
     * Returns the XMLResponse from the Payment object
     */
    public function getXMLResponse()
    {
        return $this->XMLResponse;
    }

    /**
     * Sets the BillingID on the Payment object
     * @param string $billingID
     */
    public function setBillingID($billingID)
    {
        $this->BillingID = $billingID;
    }

    /**
     * Returns the BillingID from the Payment object
     */
    public function getBillingID()
    {
        return $this->BillingID;
    }

    /**
     * Sets the HelpText on the Payment object
     * @param string $helpText
     */
    public function setHelpText($helpText)
    {
        $this->HelpText = $helpText;
    }

    /**
     * Returns the HelpText from the Payment object
     */
    public function getHelpText()
    {
        return $this->HelpText;
    }

    /**
     * Sets the ResponseCode on the Payment object
     * @param string $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->ResponseCode = $responseCode;
    }

    /**
     * Returns the ResponseCode from the Payment object
     */
    public function getResponseCode()
    {
        return $this->ResponseCode;
    }

    /**
     * Sets the SettlementDate on the Payment object
     * @param string $settlementDate
     */
    public function setSettlementDate($settlementDate)
    {
        $this->SettlementDate = $settlementDate;
    }

    /**
     * Returns the SettlementDate from the Payment object
     */
    public function getSettlementDate()
    {
        return $this->SettlementDate;
    }
}
