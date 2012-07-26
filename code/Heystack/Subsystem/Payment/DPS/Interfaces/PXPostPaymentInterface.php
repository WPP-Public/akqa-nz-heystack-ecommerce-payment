<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Interfaces namespace
 */
namespace Heystack\Subsystem\Payment\DPS\Interfaces;

/**
 * Defines methods that need to be implemented by PXPostPayments
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
interface PXPostPaymentInterface extends DPSPaymentInterface
{
    /**
     * Sets the XMLResponse on the Payment object
     * @param string $xmlResponse
     */
    public function setXMLResponse($xmlResponse);

    /**
     * Returns the XMLResponse from the Payment object
     */
    public function getXMLResponse();

    /**
     * Sets the BillingID on the Payment object
     * @param string $billingID
     */
    public function setBillingID($billingID);

    /**
     * Returns the BillingID from the Payment object
     */
    public function getBillingID();

    /**
     * Sets the HelpText on the Payment object
     * @param string $helpText
     */
    public function setHelpText($helpText);

    /**
     * Returns the HelpText from the Payment object
     */
    public function getHelpText();

    /**
     * Sets the ResponseCode on the Payment object
     * @param string $responseCode
     */
    public function setResponseCode($responseCode);

    /**
     * Returns the ResponseCode from the Payment object
     */
    public function getResponseCode();

    /**
     * Sets the SettlementDate on the Payment object
     * @param string $settlementDate
     */
    public function setSettlementDate($settlementDate);

    /**
     * Returns the SettlementDate from the Payment object
     */
    public function getSettlementDate();
}
