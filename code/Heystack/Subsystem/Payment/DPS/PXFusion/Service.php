<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;

use Heystack\Subsystem\Core\Exception\ConfigurationException;

/**
 *
 *
 * @copyright  Heyday
 * @package Ecommerce-Payment
 */
class Service implements PaymentServiceInterface
{

    use PaymentConfigTrait;

    /**
     * Auth-Complete type. This should be used when you want to authorise an amount (or just $1) to either verify a card
     * or alternatively avoid holding credit card data on the server for a later transaction
     */
    const TYPE_AUTH_COMPLETE = 'Auth-Complete';

    /**
     * Purchase type. This type should be used when you want to immediately take money from the credit card.
     */
    const TYPE_PURCHASE = 'Purchase';

    /**
     * Txn type auth. This is txnType used in the soap call when using Auth-Complete
     */
    const TXN_TYPE_AUTH = 'Auth';

    /**
     * Txn type purchase. This is the txnType used in the soap call when using Purchase
     */
    const TXN_TYPE_PURCHASE = 'Purchase';

    /**
     * Auth stage for the Auth-Complete payment cycle
     */
    const STAGE_AUTH = 'Auth';

    /**
     * Complete stage for the Auth-Complete payment cycle
     */
    const STAGE_COMPLETE = 'Complete';

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
     * Holds the data array which contains all the data specific to the payment
     * @var array
     */
    protected $data = array();

    /**
     * Hold the soap client used for connections with DPS
     * @var \SoapClient
     */
    protected $soapClient;

    /**
     * @var string
     */
    protected $stage = self::STAGE_AUTH;

    /**
     * This is the amount of money authorised in the Auth-Complete payment type
     * @var int
     */
    protected $authAmount = 1;

    /**
     * If testing last request data is needed form soap calls thi should be set to true
     * @var bool
     */
    protected $testing = false;

    /**
     * Any additional information to be passed to dps in the soap request
     * @var array
     */
    protected $additionalConfig = array();

    /**
     * Stores a list of allowed additional config params
     * @var array
     */
    protected $allowedAdditionalConfig = array(
        'enableAddBillCard',
        'avsAction',
        'avsPostCode',
        'avsStreetAddress',
        'billingId',
        'token billing',
        'dateStart',
        'enableAvsData',
        'enablePaxInfo',
        'merchantReference',
        'paxDateDepart',
        'paxName',
        'paxOrigin',
        'paxTicketNumber',
        'paxTravelAgentInfo',
        'timeout',
        'txnData1',
        'txnData2',
        'txnData3'
    );

    /**
     * Creates the PxFusionHandler object
     * @param type                                                                      $paymentClass
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface               $eventService
     * @param \Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface $transaction
     */
    public function __construct(
            $paymentClass,
            EventDispatcherInterface $eventService,
            TransactionInterface $transaction
    )
    {
        $this->paymentClass = $paymentClass;
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
            'Type',
            'Username',
            'Password',
            'Wsdl'
        );
    }

    protected function validateConfig($config)
    {

        if (!in_array($config['Type'], array(
            self::TYPE_AUTH_COMPLETE,
            self::TYPE_PURCHASE
        ))) {

            throw new ConfigurationException("{$config['Type']} is not a valid Type for this payment handler");

        }

        if (!\Director::is_absolute_url($config['Wsdl'])) {

            throw new ConfigurationException("Wsdl needs to be an absolute url");

        }

    }

    public function getType()
    {

        return isset($this->config['Type']) ? $this->config['Type'] : false;

    }

    public function setType($type)
    {

        $this->config['Type'] = $type;

        $this->validateConfig($this->config);

    }

    public function getReturnUrl()
    {

        switch ($this->config['Type']) {

            case self::TYPE_AUTH_COMPLETE:
                return \Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/auth');
            case self::TYPE_PURCHASE:
                return \Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/purchase');

        }

    }

    public function getTxnType()
    {

        if ($this->getType() == self::TYPE_AUTH_COMPLETE && $this->getStage() == self::STAGE_AUTH) {
            return self::TXN_TYPE_AUTH;
        }

        return self::TXN_TYPE_PURCHASE;

    }

    public function getSoapClient()
    {

        if (!$this->soapClient) {

            $this->soapClient = new \SoapClient(
                $this->config['Wsdl'],
                array(
                    'soap_version' => SOAP_1_1,
                    'trace' => $this->getTesting()
                )
            );

        }

        return $this->soapClient;

    }

    public function getTransactionId()
    {

        $soapClient = $this->getSoapClient();

        $configuration = array(
            'username' => $this->config['Username'],
            'password' => $this->config['Password'],
            'tranDetail' => array_merge(array(
                'txnType' => $this->getTxnType(),
                'currency' => $this->transaction->getCurrencyCode(),
                'amount' => $this->getAmount(),
                'returnUrl' => $this->getReturnUrl()
                //TODO add merchant ref is exists
                //TODO add txnRef
            ), $this->getAdditionalConfig())
        );

        $response = $soapClient->GetTransactionId($configuration);

        if (is_object($response) && $response->GetTransactionIdResult && $response->GetTransactionIdResult->success) {

            return $response->GetTransactionIdResult->sessionId;

        } else {

            throw new Exception($soapClient->__getLastResponse(), $response, $configuration);

        }

    }

    /**
     * Saves the data that comes from the payment form submission for later use
     * @param array $data
     */
    public function savePaymentData(array $data)
    {
    }

    /**
     * Prepare the payment form submission data for use when executing the payment
     * @return array
     */
    protected function prepareDataForPayment()
    {
    }

    /**
     * Check that the data is complete. Make sure that all the fields required
     * for executing the payment is present.
     * @param  array      $data
     * @return boolean
     * @throws \Exception
     */
    protected function checkPaymentData(array $data)
    {
    }

    /**
     * Execute the payment by creating the Payment object and contacting DPS to
     * handle the payment.
     * @param  type       $transactionID
     * @throws \Exception
     */
    public function executePayment($transactionID)
    {

    }

    /**
     * @param string $stage
     */
    public function setStage($stage)
    {
        if ($this->getType() == self::TYPE_AUTH_COMPLETE && in_array($stage, array(
            self::STAGE_AUTH,
            self::STAGE_COMPLETE
        ))) {
            $this->stage = $stage;
        } else {
            throw new ConfigurationException('Auth and Complete are the only supported stages');
        }
    }

    /**
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
    }

    public function getAmount()
    {

        switch ($this->getTxnType()) {
            case 'Auth':
                return number_format($this->authAmount, 2);
            default:
                return number_format($this->transaction->getTotal(), 2);
        }

    }

    public function completeTransaction()
    {

        $this->setStage('Complete');

    }

    /**
     * @param int $authAmount
     */
    public function setAuthAmount($authAmount)
    {
        $this->authAmount = $authAmount;
    }

    /**
     * @return int
     */
    public function getAuthAmount()
    {
        return $this->authAmount;
    }

    /**
     * @param boolean $testing
     */
    public function setTesting($testing)
    {
        $this->testing = $testing;
    }

    /**
     * @return boolean
     */
    public function getTesting()
    {
        return $this->testing;
    }

    /**
     * @param array $additionalConfig
     */
    public function setAdditionalConfig(array $additionalConfig)
    {

        $this->additionalConfig = array_flip(array_intersect(array_flip($additionalConfig), $this->allowedAdditionalConfig));

    }

    /**
     * @return array
     */
    public function getAdditionalConfig()
    {
        return $this->additionalConfig;
    }

    /**
     * @param array $allowedAdditionalConfig
     */
    public function setAllowedAdditionalConfig($allowedAdditionalConfig)
    {
        $this->allowedAdditionalConfig = $allowedAdditionalConfig;
    }

    /**
     * @return array
     */
    public function getAllowedAdditionalConfig()
    {
        return $this->allowedAdditionalConfig;
    }

}
