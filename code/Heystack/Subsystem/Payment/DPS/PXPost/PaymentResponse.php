<?php
/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXPost;

use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\Traits\ParentReferenceTrait;
use Heystack\Subsystem\Core\ViewableData\ViewableDataInterface;

/**
 * Payment stores information about payments made with the PXPost method
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @package Heystack
 *
 */
class PaymentResponse implements StorableInterface, ViewableDataInterface
{

    use ParentReferenceTrait;

    const IDENTIFIER = 'pxpostpayment';

    const SCHEMA_NAME = 'PXPostPayment';

    protected $data = array();

    protected $mapping = array(
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

    /**
     * Get the name of the schema this system relates to
     * @return string
     */
    public function getSchemaName()
    {
        return self::SCHEMA_NAME;
    }

    public function getStorableData()
    {
        $data = array();

        $data['id'] = $this->getSchemaName();

        $data['flat'] = array(
            'Status' => $this->getStatus(),
            'CurrencyCode' => $this->getCurrencyCode(),
            'Message' => $this->getMessage(),
            'Amount' => $this->getAmount(),
            'IP' => $this->getIP(),
            'TransactionType' => $this->getTransactionType(),
            'MerchantReference' => $this->getMerchantReference(),
            'TransactionReference' => $this->getTransactionReference(),
            'AuthCode' => $this->getAuthCode(),
            'XMLResponse' => $this->getXMLResponse(),
            'BillingID' => $this->getBillingID(),
            'HelpText' => $this->getHelpText(),
            'ResponseCode' => $this->getResponseCode(),
            'SettlementDate' => $this->getSettlementDate(),
            'ParentID' => $this->parentReference
        );

        $data['parent'] = true;

        $data['related'] = false;

        return $data;

    }

    /**
     * @todo document this
     * @return string
     */
    public function getStorableBackendIdentifiers()
    {
        return array(
            Backend::IDENTIFIER
        );
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
            'Authorized',
            'ReCo',
            'RxDate',
            'RxDateLocal',
            'LocalTimeZone',
            'MerchantReference',
            'CardName',
            'Retry',
            'StatusRequired',
            'AuthCode',
            'AmountBalance',
            'Amount',
            'CurrencyId',
            'InputCurrencyId',
            'InputCurrencyName',
            'CurrencyRate',
            'CurrencyName',
            'CardHolderName',
            'DateSettlement',
            'TxnType',
            'CardNumber',
            'TxnMac',
            'DateExpiry',
            'ProductId',
            'AcquirerDate',
            'AcquirerTime',
            'AcquirerId',
            'Acquirer',
            'AcquirerReCo',
            'AcquirerResponseText',
            'TestMode',
            'CardId',
            'CardHolderResponseText',
            'CardHolderHelpText',
            'CardHolderResponseDescription',
            'MerchantResponseText',
            'MerchantHelpText',
            'MerchantResponseDescription',
            'UrlFail',
            'UrlSuccess',
            'EnablePostResponse',
            'PxPayName',
            'PxPayLogoSrc',
            'PxPayUserId',
            'PxPayXsl',
            'PxPayBgColor',
            'PxPayOptions',
            'Cvc2ResultCode',
            'AcquirerPort',
            'AcquirerTxnRef',
            'GroupAccount',
            'DpsTxnRef',
            'AllowRetry',
            'DpsBillingId',
            'BillingId',
            'TransactionId',
            'PxHostId',
            'RmReason',
            'RmReasonId',
            'RiskScore',
            'RiskScoreText'
        );
    }
}