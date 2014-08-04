<?php
/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Payment\DPS\PXFusion;

use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\StorableInterface;
use Heystack\Core\ViewableData\ViewableDataInterface;

/**
 * Class PaymentResponse
 * @package Heystack\Payment\DPS\PXFusion
 */
class PaymentResponse implements StorableInterface, ViewableDataInterface
{
    /**
     *
     */
    const IDENTIFIER = 'pxfusionpayment';
    /**
     *
     */
    const SCHEMA_NAME = 'PXFusionPayment';

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $mapping = [
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
    ];

    /**
     * @param array $data
     */
    public function __construct($data)
    {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $this->mapping)) {
                $this->data[$this->mapping[$key]] = $value;
            }
        }
    }

    /**
     * @param string $name
     * @return mixed|null|bool
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : false;
    }

    /**
     * @param string $name
     * @param mixed|null $value
     * @return void
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @return string
     */
    public function getStorableIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * @return array
     */
    public function getStorableData()
    {
        return [
            'id' => self::SCHEMA_NAME,
            'flat' => $this->data
        ];
    }

    /**
     * @return array
     */
    public function getStorableBackendIdentifiers()
    {
        return [
            Backend::IDENTIFIER
        ];
    }

    /**
     * @return string
     */
    public function getSchemaName()
    {
        return self::SCHEMA_NAME;
    }

    /**
     * Defines what methods the implementing class implements dynamically through __get and __set
     * @return array
     */
    public function getDynamicMethods()
    {
        return array_keys($this->data);
    }

    /**
     * Returns an array of SilverStripe DBField castings keyed by field name
     * @return array
     */
    public function getCastings()
    {
        return [
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
        ];
    }

}
