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

use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\Storage\Traits\ParentReferenceTrait;
use Heystack\Subsystem\Core\ViewableData\ViewableDataInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;

/**
 * Payment stores information about payments made with the PXPost method
 *
 * @copyright  Heyday
 * @author     Glenn Bautista <glenn@heyday.co.nz>
 * @author     Stevie Mayhew <stevie@heyday.co.nz>
 * @package    Heystack
 *
 */
class PaymentResponse implements StorableInterface, ViewableDataInterface
{

    use ParentReferenceTrait;

    /**
     *
     */
    const IDENTIFIER = 'pxpostpayment';

    /**
     *
     */
    const SCHEMA_NAME = 'PXPostPayment';

    /**
     * @var array
     */
    protected $data = array();

    /**
     * @var array
     */
    protected $allowedFields = array(
        'ReCo',
        'ResponseText',
        'HelpText',
        'Success',
        'DpsTxnRef',
        'TxnRef',
        'RmReason',
        'RmReasonId',
        'RiskScore',
        'RiskScoreText',
        'Authorized',
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
        'AllowRetry',
        'DpsBillingId',
        'BillingId',
        'TransactionId',
        'PxHostId'
    );

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $this->allowedFields)) {
                    $this->data[$key] = $value;
                }
            }
        } else {
            throw new \Exception('payment response data must be an array');
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function __get($name)
    {
        return array_key_exists($name, $this->data) ? $this->data[$name] : false;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * @param TransactionInterface $transaction
     */
    public function updateTransaction(TransactionInterface $transaction)
    {

        if ($this->Success) {

            $transaction->setStatus('Successful');

        } else {

            $transaction->setStatus('Failed');

        }

    }

    /**
     * @return string
     */
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

    /**
     * @return array
     */
    public function getStorableData()
    {
        return array(
            'id'      => $this->getSchemaName(),
            'flat'    => array_merge(
                $this->data,
                array(
                    'ParentID' => $this->parentReference
                )
            ),
            'parent'  => true,
            'related' => false
        );
    }

    /**
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
            'ReCo'                          => 'Varchar',
            'ResponseText'                  => 'Varchar',
            'HelpText'                      => 'Varchar',
            'Success'                       => 'Int',
            'DpsTxnRef'                     => 'Varchar',
            'TxnRef'                        => 'Varchar',
            'RmReason'                      => 'Varchar',
            'RmReasonId'                    => 'Varchar',
            'RiskScore'                     => 'Int',
            'RiskScoreText'                 => 'Text',
            'Authorized'                    => 'Int',
            'RxDate'                        => 'Varchar',
            'RxDateLocal'                   => 'Varchar',
            'LocalTimeZone'                 => 'Varchar',
            'MerchantReference'             => 'Varchar',
            'CardName'                      => 'Varchar',
            'Retry'                         => 'Int',
            'StatusRequired'                => 'Int',
            'AuthCode'                      => 'Varchar',
            'AmountBalance'                 => 'Decimal',
            'Amount'                        => 'Decimal',
            'CurrencyId'                    => 'Int',
            'InputCurrencyId'               => 'Int',
            'InputCurrencyName'             => 'Varchar',
            'CurrencyRate'                  => 'Decimal',
            'CurrencyName'                  => 'Varchar',
            'CardHolderName'                => 'Varchar',
            'DateSettlement'                => 'Date',
            'TxnType'                       => 'Varchar',
            'CardNumber'                    => 'Varchar',
            'TxnMac'                        => 'Varchar',
            'DateExpiry'                    => 'Varchar',
            'ProductId'                     => 'Int',
            'AcquirerDate'                  => 'Date',
            'AcquirerTime'                  => 'Varchar',
            'AcquirerId'                    => 'Int',
            'Acquirer'                      => 'Varchar',
            'AcquirerReCo'                  => 'Varchar',
            'AcquirerResponseText'          => 'Text',
            'TestMode'                      => 'Int',
            'CardId'                        => 'Int',
            'CardHolderResponseText'        => 'Text',
            'CardHolderHelpText'            => 'Varchar',
            'CardHolderResponseDescription' => 'Varchar',
            'MerchantResponseText'          => 'Text',
            'MerchantHelpText'              => 'Varchar',
            'MerchantResponseDescription'   => 'Varchar',
            'UrlFail'                       => 'Varchar',
            'UrlSuccess'                    => 'Varchar',
            'EnablePostResponse'            => 'Int',
            'AcquirerPort'                  => 'Varchar',
            'AcquirerTxnRef'                => 'Varchar',
            'GroupAccount'                  => 'Varchar',
            'AllowRetry'                    => 'Int',
            'DpsBillingId'                  => 'Varchar',
            'BillingId'                     => 'Varchar',
            'TransactionId'                 => 'Varchar',
            'PxHostId'                      => 'Varchar'
        );
    }
}
