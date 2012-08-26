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

use Heystack\Subsystem\Payment\DPS\Service as BaseService;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
class Service extends BaseService
{

    /**
     * Holds the key for storing Post Username on the config second level array on the data array
     */
    const CONFIG_USERNAME = 'Username';

    /**
     * Holds the key for storing Post Password on the config second level array on the data array
     */
    const CONFIG_PASSWORD = 'Password';

    /**
     * Holds the key for storing the user data card number
     */
    const CONFIG_USER_DATA_CARD_NUMBER = 'CardNumber';

    /**
     * Holds the key for storing the user data card holder name
     */
    const CONFIG_USER_DATA_CARD_HOLDER_NAME = 'CardHolderName';

    /**
     * Holds the key for storing the user data date expiry
     */
    const CONFIG_USER_DATA_CARD_DATE_EXPIRY = 'DateExpiry';

    /**
     * Holds the key for storing the user data cvc2
     */
    const CONFIG_USER_DATA_CARD_CVC2 = 'Cvc2';

    /**
     * Txn type purchase. Used for immediate purchases
     */
    const TXN_TYPE_PURCHASE = 'Purchase';

    /**
     * Txn type auth. Used for authorisations to be completed at a later time
     */
    const TXN_TYPE_AUTH = 'Auth';

    /**
     * Txn type complete. Used to complete authorisations
     */
    const TXN_TYPE_COMPLETE = 'Complete';

    /**
     * Txn type refund. Used to refund transactions
     */
    const TXN_TYPE_REFUND = 'Refund';

    /**
     * Txn type refund. Used to refund transactions
     */
    const TXN_TYPE_VALIDATE = 'Validate';

    /**
     * Holds the Event Dispatcher service
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventService;

    /**
     * Holds the Transaction object
     * @var \Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface
     */

    /**
     * Holds the default gateway url
     * @var string
     */
    protected $gatewayUrl = 'https://sec.paymentexpress.com/pxpost.aspx';

    /**
     * Holds the txn type to be used in the request
     * @var string
     */
    protected $txnType = self::TXN_TYPE_PURCHASE;

    /**
     * Creates the Service
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
     * @return \Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface
     */
    public function getTransaction()
    {
        return $this->transaction;
    }

    /**
     * @return array
     */
    protected function getAllowedConfig()
    {
        return array(
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD
        );
    }

    /**
     * Defines an array of required parameters used in setConfig
     * @return array
     */
    protected function getRequiredConfig()
    {
        return array(
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD
        );
    }

    protected function getAllowedUserConfig()
    {
        return array(
            self::CONFIG_USER_DATA_CARD_NUMBER,
            self::CONFIG_USER_DATA_CARD_HOLDER_NAME,
            self::CONFIG_USER_DATA_CARD_DATE_EXPIRY,
            self::CONFIG_USER_DATA_CARD_CVC2
        );
    }

    /**
     * @return array
     */
    protected function getRequiredUserConfig()
    {
        if (in_array($this->getTxnType(), array(
            self::TXN_TYPE_AUTH,
            self::TXN_TYPE_PURCHASE,
            self::TXN_TYPE_VALIDATE
        ))) {
            return array(
                self::CONFIG_USER_DATA_CARD_NUMBER
            );
        }
        return array();
    }

    /**
     * Gets the allowed options for the additional configuration
     * @return array
     */
    public function getAllowedAdditionalConfig()
    {
        return array(
            'BillingId',
            'DpsBillingId',
            'DpsTxnRef',
            'EnableAddBillCard',
            'MerchantReference',
            'TxnData1',
            'TxnData2',
            'TxnData3',
            'TxnId',
            'EnableAvsData',
            'AvsAction',
            'AvsPostCode',
            'AvsStreetAddress',
            'DateStart',
            'IssueNumber',
            'Track2'
        );
    }

    public function getRequiredAdditionalConfig()
    {
        if (in_array($this->getTxnType(), array(
            self::TXN_TYPE_COMPLETE,
            self::TXN_TYPE_REFUND
        ))) {
            return array(
                'DpsTxnRef'
            );
        }

        return array();
    }

    /**
     * @return array
     */
    protected function validateConfig(array $config)
    {
        return array();
    }

    /**
     * @return array
     */
    protected function validateAdditionalConfig(array $config)
    {
        return array();
    }

    /**
     * @return array
     */
    protected function validateUserConfig(array $config)
    {
        return array();
    }

    /**
     * Returns a configuration array with different information based on what type of request is being made
     * @return array
     * @throws \Heystack\Subsystem\Core\Exception\ConfigurationException
     */
    protected function config()
    {
        $txnType = $this->getTxnType();

        $config = array_merge(
            array(
                'PostUsername' => $this->config[self::CONFIG_USERNAME],
                'PostPassword' => $this->config[self::CONFIG_PASSWORD],
                'TxnType' => $txnType,
                'Amount' => $this->getAmount()
            ),
            $this->getAdditionalConfig()
        );

        if (in_array($txnType, array(
            self::TXN_TYPE_AUTH,
            self::TXN_TYPE_PURCHASE,
            self::TXN_TYPE_VALIDATE
        ))) {

            $config = array_merge(
                $config,
                $this->getUserConfig(),
                array(
                    'InputCurrency' => $this->getCurrencyCode()
                )
            );

        }

        return $config;
    }

    /**
     * Builds xml string for use in payment request
     * @param $config
     * @return mixed
     */
    private function buildXml($config)
    {
        $xml = new \SimpleXMLElement('<Txn></Txn>');

        foreach ($config as $key => $value) {
            $xml->$key = $value;
        }

        return $xml->asXML();
    }

    /**
     * Processes and authorization transaction
     */
    public function processAuthorize()
    {
        $this->setTxnType(self::TXN_TYPE_AUTH);
        $errors = $this->checkAll();
        if ($this->hasErrors($errors)) {
            $response = $this->responseFromErrors($errors);
        } else {
            $response = $this->responseFromResult($this->process());
        }
        file_put_contents('results.txt', print_r($response, true));
        return $response;
    }

    /**
     * Completes a transaction
     */
    public function processComplete()
    {
        $this->setTxnType(self::TXN_TYPE_COMPLETE);
        $errors = $this->checkAll();
        if ($this->hasErrors($errors)) {
            $response = $this->responseFromErrors($errors);
        } else {
            $response = $this->responseFromResult($this->process());
        }
        return $response;
    }

    /**
     *
     */
    public function processPurchase()
    {
        $this->setTxnType(self::TXN_TYPE_PURCHASE);
        $errors = $this->checkAll();
        if ($this->hasErrors($errors)) {
//            $response = $this->responseFromErrors($errors);
        } else {
//            $response = $this->responseFromResult($this->process());
            var_dump($this->process());
            die;
        }
        return $response;
    }

    /**
     *
     */
    public function processRefund()
    {
        $this->setTxnType(self::TXN_TYPE_REFUND);
        $errors = $this->checkAll();
        if ($this->hasErrors($errors)) {
            $response = $this->responseFromErrors($errors);
        } else {
            $response = $this->responseFromResult($this->process());
        }
        return $response;
    }

    /**
     *
     */
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
            $response = new \SimpleXMLElement($resultXml);
            $result = json_decode(json_encode((array) $response->Transaction), true);
        } catch (\Exception $e) {
            $result = null;
        }

        return $result;
    }

    /**
     * @param $gatewayUrl
     * @throws \Heystack\Subsystem\Core\Exception\ConfigurationException
     */
    public function setGatewayUrl($gatewayUrl)
    {
        if (!\Director::is_absolute_url($gatewayUrl)) {

            throw new ConfigurationException("Gateway url needs to be an absolute url");

        }

        $this->gatewayUrl = $gatewayUrl;
    }

    /**
     * @return string
     */
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
            self::TXN_TYPE_REFUND,
            self::TXN_TYPE_VALIDATE
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
     * @return string
     */
    public function getAmount()
    {
        return number_format($this->getTransaction()->getTotal(), 2);
    }

}
