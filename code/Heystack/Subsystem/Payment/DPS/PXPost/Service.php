<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXPost;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;

use Heystack\Subsystem\Payment\Events;
use Heystack\Subsystem\Payment\Events\PaymentEvent;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Subsystem\Core\Exception\ConfigurationException;

/**
 * Contains the main logic for creating Payment objects as well as interfacing
 * with DPS's PXPost API
 *
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Ecommerce-Payment
 */
class Service implements PaymentServiceInterface
{

    use PaymentConfigTrait;

    /**
     * Holds the key for storing payment data on the data array
     */
    const PAYMENT_DATA_KEY = 'paymentdatakey';

    /**
     * Holds the key for storing Post Username on the config second level array on the data array
     */
    const CONFIG_USERNAME = 'Username';

    /**
     * Holds the key for storing Post Password on the config second level array on the data array
     */
    const CONFIG_PASSWORD = 'Password';

    /**
     *
     */
    const CONFIG_USER_DATA_CARD_NUMBER = 'CardNumber';

    /**
     *
     */
    const CONFIG_USER_DATA_CARD_HOLDER_NAME = 'CardHolderName';

    /**
     *
     */
    const CONFIG_USER_DATA_CARD_DATE_EXPIRY = 'DateExpiry';

    /**
     *
     */
    const CONFIG_USER_DATA_CARD_CVC2 = 'Cvc2';

    /**
     *
     */
    const TXN_TYPE_PURCHASE = 'Purchase';

    /**
     *
     */
    const TXN_TYPE_AUTH = 'Auth';

    /**
     *
     */
    const TXN_TYPE_COMPLETE = 'Complete';

    /**
     *
     */
    const TXN_TYPE_REFUND = 'Refund';

    /**
     * Holds the Event Dispatcher service
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventService;

    /**
     * Holds the Transaction object
     * @var \Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface
     */
    protected $transaction;

    /**
     *
     * @var array
     */
    protected $userData = array();

    /**
     * If testing last request data is needed form soap calls thi should be set to true
     * @var bool
     */
    protected $testing = false;

    /**
     * Holds the default gateway url
     */
    protected $gatewayUrl = 'https://sec.paymentexpress.com/pxpost.aspx';

    /**
     *
     * @var string
     */
    protected $txnType = self::TXN_TYPE_PURCHASE;

    /**
     * List of currencies supported by DPS
     * @var array
     */
    protected $supportedCurrencies = array(
        'CAD',
        'CHF',
        'DKK',
        'EUR',
        'FRF',
        'GBP',
        'HKD',
        'JPY',
        'NZD',
        'SGD',
        'THB',
        'USD',
        'ZAR',
        'AUD',
        'WST',
        'VUV',
        'TOP',
        'SBD',
        'PGK',
        'MYR',
        'KWD',
        'FJD'
    );

    /**
     * Creates the PxPostHandler object
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface               $eventService
     * @param \Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface $transaction
     */
    public function __construct(
            EventDispatcherInterface $eventService,
            TransactionInterface $transaction
    ) {
        $this->eventService = $eventService;
        $this->transaction = $transaction;
    }

    /**
     * Defines an array of required parameters used in setConfig
     * @return array
     */
    protected function getRequiredConfigParameters()
    {
        return array(
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD
        );
    }

    protected function getRequiredUserData()
    {
        return array(
            self::CONFIG_USER_DATA_CARD_NUMBER,
            self::CONFIG_USER_DATA_CARD_HOLDER_NAME,
            self::CONFIG_USER_DATA_CARD_DATE_EXPIRY,
            self::CONFIG_USER_DATA_CARD_CVC2
        );
    }

    public function validateConfig()
    {
        return true;
    }

//
//    /**
//     * Saves the data that comes from the payment form submission for later use
//     * @param array $data
//     */
//    public function savePaymentData(array $data)
//    {
//        unset($data['url']);
//        $this->data[self::PAYMENT_DATA_KEY] = $data;
//
//        $this->eventService->dispatch(TransactionEvents::STORE);
//    }
//
//    /**
//     * Prepare the payment form submission data for use when executing the payment
//     * @return array
//     */
//    protected function prepareDataForPayment()
//    {
//        $data = $this->data[self::PAYMENT_DATA_KEY];
//
//        $data['PostUsername'] = $this->config[self::POST_USERNAME];
//        $data['PostPassword'] = $this->config[self::POST_PASSWORD];
//        $data['Amount'] = $this->transaction->getTotal();
//        $data['InputCurrency'] = $this->transaction->getCurrencyCode();
//        $data['TxnType'] = 'Purchase';
//
//        return $this->checkPaymentData($data) ? $data : null;
//    }
//
//    /**
//     * Check that the data is complete. Make sure that all the fields required
//     * for executing the payment is present.
//     * @param  array      $data
//     * @return boolean
//     * @throws ConfigurationException
//     */
//    protected function checkPaymentData(array $data)
//    {
//
//        $required = array(
//            'PostUsername',
//            'PostPassword',
//            'CardHolderName',
//            'CardNumber',
//            'Cvc2'
//        );
//
//        $missing = array_diff($required, array_keys($data));
//
//        if (!count($missing)) {
//            return true;
//        } else {
//            throw new ConfigurationException('The following required fields are missing: ' . implode(', ', $missing));
//        }
//
//        return false;
//    }

    protected function config()
    {

        $txnType = $this->getTxnType();

        $config = array(
            'PostUsername' => $this->config[self::CONFIG_USERNAME],
            'PostPassword' => $this->config[self::CONFIG_PASSWORD],
            'TxnType' => $txnType,
            'Amount' => $this->getAmount()
        );

        if ($txnType === self::TXN_TYPE_AUTH || $txnType === self::TXN_TYPE_PURCHASE) {

            if (!$this->hasUserData()) {

                throw new ConfigurationException('User data has not be supplied for PXPost');

            }

            $config = array_merge($config, $this->getUserData(), array(
                'InputCurrency' => $this->getCurrencyCode(),
                'MerchantReference' => $this->getMerchantReference(),
                'EnableAddBillCard' => $this->getEnableAddBillCard()
            ));

        }

        return $config;

    }

    private function buildXml($config)
    {
        $xml = new \SimpleXMLElement('<Txn></Txn>');

        foreach ($config as $key => $value) {
            $xml->$key = $value;
        }

        return $xml->asXML();
    }

    public function processAuthorize()
    {
        $this->setTxnType(self::TXN_TYPE_AUTH);
        $this->process();
    }

    public function processComplete()
    {
        $this->setTxnType(self::TXN_TYPE_COMPLETE);
        $this->process();
    }

    public function processPurchase()
    {
        $this->setTxnType(self::TXN_TYPE_PURCHASE);
        $this->process();
    }

    public function processRefund()
    {
        $this->setTxnType(self::TXN_TYPE_REFUND);
        $this->process();
    }

    public function process()
    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->getGatewayUrl());
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->buildXml($this->config()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSLVERSION, 3);

        $resultXml = curl_exec($curl);
        curl_close($curl);

        try {
            $result = new \SimpleXMLElement($resultXml);
        } catch (\Exception $e) {
            $result = null;
        }

//        $this->eventService->dispatch();

        var_dump($result);
    }

    /**
     *
     * @return string
     */
    public function getAmount()
    {
        return number_format($this->transaction->getTotal(), 2);
    }

    /**
     * Returns the currency code.
     * @return mixed
     * @throws ConfigurationException
     */
    protected function getCurrencyCode()
    {
        $currencyCode = $this->transaction->getCurrencyCode();

        if (!in_array($currencyCode, $this->supportedCurrencies)) {

            throw new ConfigurationException("The currency $currencyCode is not supported by PXPost");

        }

        return $currencyCode;
    }

    /**
     * Set the testing mode
     * @param boolean $testing
     */
    public function setTesting($testing)
    {
        $this->testing = $testing;
    }

    /**
     * Get the testing mode
     * @return boolean
     */
    public function getTesting()
    {
        return $this->testing;
    }

    public function setGatewayUrl($gatewayUrl)
    {
        if (!\Director::is_absolute_url($gatewayUrl)) {

            throw new ConfigurationException("Gateway url needs to be an absolute url");

        }

        $this->gatewayUrl = $gatewayUrl;
    }

    public function getGatewayUrl()
    {
        return $this->gatewayUrl;
    }

    /**
     *
     * @param string $txnType
     * @throws \Heystack\Subsystem\Core\Exception\ConfigurationException
     * @return void
     */
    public function setTxnType($txnType)
    {
        if (!in_array($txnType, array(
            self::TXN_TYPE_PURCHASE,
            self::TXN_TYPE_AUTH,
            self::TXN_TYPE_COMPLETE,
            self::TXN_TYPE_REFUND
        ))) {

            throw new ConfigurationException('PXPost only supports Purchase, Auth, Complete and Refund txn types');

        }

        $this->txnType = $txnType;
    }

    /**
     * @return string
     */
    public function getTxnType()
    {
        return $this->txnType;
    }

    /**
     *
     * @param array $userData
     * @throws \Heystack\Subsystem\Core\Exception\ConfigurationException
     * @return void
     */
    public function setUserData($userData)
    {
        if (count(array_diff($this->getRequiredUserData(), array_keys($userData))) !== 0) {
            throw new ConfigurationException('There is a problem with your user data');
        }

        $this->userData = $userData;
    }

    /**
     * @return array
     */
    public function getUserData()
    {
        return $this->userData;
    }

    public function hasUserData()
    {
        return count($this->userData) !== 0;
    }

    public function getMerchantReference()
    {
        //TODO
        return '';
    }

    public function getEnableAddBillCard()
    {
        //TODO
        return false;
    }

}
