<?php
/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\ViewableData\ViewableDataInterface;

class PaymentResponse implements StorableInterface, ViewableDataInterface
{

    const IDENTIFIER = 'pxfusionpayment';
    const SCHEMA_NAME = 'PXFusionPayment';

    protected $data = array();

    protected $mapping = array(
        'merchantReference' => 'MerchantReference',
        'amount' => 'Amount',
        'authCode' => 'AuthCode',
        'billingId' => 'BillingID',
        'cardHolderName' => 'CardHolderName',
        'cardName' => 'CardName',
        'cardNumber' => 'CardNumber',
        'cardNumber2' => 'CardNumber2',
        'currencyId' => 'CurrencyId',
        'currencyRate' => 'CurrencyRate',
        'dateExpiry' => 'DateExpiry',
        'dateSettlement' => 'DateSettlement',
        'dpsBillingId' => 'DpsBillingId',
        'dpsTxnRef' => 'DpsTxnRef',
        'responseCode' => 'ResponseCode',
        'responseText' => 'ResponseText',
        'sessionId' => 'SessionId',
        'status' => 'Status',
        'statusCode' => 'StatusCode',
        'testMode' => 'TestMode',
        'transactionId' => 'TransactionId',
        'txnData1' => 'TxnData1',
        'txnData2' => 'TxnData2',
        'txnData3' => 'TxnData3',
        'txnMac' => 'TxnMac',
        'txnRef' => 'TxnRef',
        'txnType' => 'TransactionType'
    );

    function __construct($data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->mapping)) {
                $this->data[$this->mapping[$key]] = $value;
            }
        }
    }

    function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : false;
    }

    function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function getStorableIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function getStorableData()
    {
        return array(
            'id' => self::SCHEMA_NAME,
            'flat' => $this->data
        );
    }

    public function getStorableBackendIdentifiers()
    {
        return array(
            Backend::IDENTIFIER
        );
    }

    public function getSchemaName()
    {
        return self::SCHEMA_NAME;
    }

    /**
     * Defines what methods the implementing class implements dynamically through __get and __set
     */
    public function getDynamicMethods()
    {
        return array_keys($this->data);
    }

    /**
     * Returns an array of SilverStripe DBField castings keyed by field name
     */
    public function getCastings()
    {
        return array(
            'MerchantReference' => 'Varchar',
            'Amount' => 'Decimal',
            'AuthCode' => 'Varchar',
            'BillingID' => 'Varchar',
            'CardHolderName' => 'Varchar',
            'CardName' => 'Varchar',
            'CardNumber' => 'Varchar',
            'CardNumber2' => 'Varchar',
            'CurrencyId' => 'Varchar',
            'CurrencyRate' => 'Varchar',
            'DateExpiry' => 'Varchar',
            'DateSettlement' => 'Varchar',
            'DpsBillingId' => 'Varchar',
            'DpsTxnRef' => 'Varchar',
            'ResponseCode' => 'Varchar',
            'ResponseText' => 'Varchar',
            'SessionId' => 'Varchar',
            'Status' => 'Enum',
            'StatusCore' => 'Int',
            'TestMode' => 'Boolean',
            'TransactionId' => 'Varchar',
            'TxnData1' => 'Varchar',
            'TxnData2' => 'Varchar',
            'TxnData3' => 'Varchar',
            'TxnMac' => 'Varchar',
            'TxnRef' => 'Varchar',
            'TransactionType' => 'Enum'
        );
    }

}
