<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Payment\DPS\PXFusion;

use Heystack\Core\EventDispatcher;
use Heystack\Core\Exception\ConfigurationException;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Ecommerce\Transaction\Interfaces\HasTransactionInterface;
use Heystack\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Ecommerce\Transaction\Traits\HasTransactionTrait;
use Heystack\Payment\DPS\PXPost\Service as PXPostService;
use Heystack\Payment\DPS\Service as BaseService;
use SebastianBergmann\Money\Money;

/**
 *
 *
 * @copyright  Heyday
 * @package    Ecommerce-Payment
 */
class Service extends BaseService implements HasTransactionInterface
{
    use HasEventServiceTrait;
    use HasTransactionTrait;

    /**
     * Config key for type
     */
    const CONFIG_TYPE = 'Type';

    /**
     * Config key for username
     */
    const CONFIG_USERNAME = 'Username';

    /**
     * Config key for password
     */
    const CONFIG_PASSWORD = 'Password';

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
     * Holds the px post service for when using the auth complete cycle
     * @var \Heystack\Payment\DPS\PXPost\Service
     */
    protected $pxPostService;

    /**
     * Holds the data array which contains all the data specific to the payment
     * @var array
     */
    protected $data = [];

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
     *
     * This amount is in the smallest currency unit, i.e. $1 = 100
     * @var int
     */
    protected $authAmount = 100;

    /**
     * Holds the testing server URL
     * @var string
     */
    protected $testingServerUrl = 'https://qa4.paymentexpress.com';

    /**
     * Holds the production server URL
     * @var string
     */
    protected $liveServerUrl = 'https://sec.paymentexpress.com';

    /**
     * Default wsdl for Soap client
     * @var string
     */
    protected $wsdl = '/pxf/pxf.svc?wsdl';

    /**
     * List of messages for each status code
     * @var array
     */
    protected $statusMessages = [
        0 => 'Approved',
        1 => 'Declined',
        2 => 'Declined due to temporary error, please retry',
        3 => 'There was an error with your transaction, please contact the site admin',
        4 => 'Transaction result cannot be determined at this time (re-run GetTransaction)',
        5 => 'Transaction did not proceed due to being attempted after timeout timestamp or having been cancelled by a CancelTransaction call',
        6 => 'No transaction found (SessionId query failed to return a transaction record - transaction not yet attempted)'
    ];

    /**
     * @var array
     */
    protected $errorStatuses = [
        2,
        3,
        4,
        5,
        6
    ];

    /**
     * Creates the Service object
     * @param \Heystack\Core\EventDispatcher $eventService
     * @param \Heystack\Ecommerce\Transaction\Interfaces\TransactionInterface $transaction
     * @param \Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface $currencyService
     * @param \Heystack\Payment\DPS\PXPost\Service $pxPostService
     */
    public function __construct(
        EventDispatcher $eventService,
        TransactionInterface $transaction,
        CurrencyServiceInterface $currencyService,
        PXPostService $pxPostService = null
    ) {
        $this->eventService = $eventService;
        $this->transaction = $transaction;
        if (!is_null($pxPostService)) {
            $this->pxPostService = $pxPostService;
        }
        $this->currencyService = $currencyService;
    }

    /**
     * Defines an array of required parameters used in setConfig
     * @return array
     */
    protected function getRequiredConfig()
    {
        return [
            self::CONFIG_TYPE,
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD
        ];
    }

    /**
     * Defines an array of required parameters used in setConfig
     * @return array
     */
    protected function getAllowedConfig()
    {
        return [
            self::CONFIG_TYPE,
            self::CONFIG_USERNAME,
            self::CONFIG_PASSWORD
        ];
    }

    /**
     * Gets the allowed options for the additional configuration
     * @return array
     */
    public function getAllowedAdditionalConfig()
    {
        return [
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
        ];
    }

    /**
     * @return array
     */
    protected function getRequiredAdditionalConfig()
    {
        return [];
    }

    /**
     * @param  array $config
     * @return array
     */
    protected function validateConfig(array $config)
    {
        $errors = [];

        if (isset($config[self::CONFIG_TYPE]) && !in_array(
                $config[self::CONFIG_TYPE],
                [
                    self::TYPE_AUTH_COMPLETE,
                    self::TYPE_PURCHASE
                ]
            )
        ) {
            $errors[] = "{$config[self::CONFIG_TYPE]} is not a valid 'Type' for this payment handler";
        }

        return $errors;
    }

    /**
     * Returns an array of required parameters used in setConfig
     * @return array
     */
    protected function getRequiredUserConfig()
    {
        return [];
    }

    /**
     * Returns an array of allowed config parameters
     * @return array
     */
    protected function getAllowedUserConfig()
    {
        return [];
    }

    /**
     * Validates config
     * @param array $config
     * @return array
     */
    protected function validateUserConfig(array $config)
    {
        return [];
    }

    /**
     * @param array $config
     * @return array
     */
    protected function validateAdditionalConfig(array $config)
    {
        return [];
    }

    /**
     * @return string|bool
     */
    public function getType()
    {
        return isset($this->config[self::CONFIG_TYPE]) ? $this->config[self::CONFIG_TYPE] : false;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->config[self::CONFIG_TYPE] = $type;

        $this->validateConfig($this->config);
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        $returnUrl = 'ecommerce/input/process/' . InputProcessor::IDENTIFIER;
        switch ($this->config[self::CONFIG_TYPE]) {
            case self::TYPE_AUTH_COMPLETE:
                $returnUrl .= '/check/auth';
                break;
            case self::TYPE_PURCHASE:
                $returnUrl .= '/check/purchase';
                break;
        }

        return \Director::protocolAndHost() . '/' . $returnUrl;
    }

    /**
     * @return string
     */
    public function getTxnType()
    {
        if ($this->getType() == self::TYPE_AUTH_COMPLETE && $this->getStage() == self::STAGE_AUTH) {
            return self::TXN_TYPE_AUTH;
        }

        return self::TXN_TYPE_PURCHASE;
    }

    /**
     * @return \SoapClient
     */
    public function getSoapClient()
    {
        if (!$this->soapClient) {

            $this->soapClient = new \SoapClient(
                $this->getWsdl(),
                [
                    'soap_version' => SOAP_1_1,
                    'trace'        => $this->getTestingMode()
                ]
            );

        }

        return $this->soapClient;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getTransactionId()
    {
        $soapClient = $this->getSoapClient();

        $configuration = [
            'username'   => $this->config[self::CONFIG_USERNAME],
            'password'   => $this->config[self::CONFIG_PASSWORD],
            'tranDetail' => array_merge(
                [
                    'txnType'   => $this->getTxnType(),
                    'currency'  => $this->getCurrencyCode(),
                    'amount'    => $this->getAmount(),
                    'returnUrl' => $this->getReturnUrl()
                ],
                $this->getAdditionalConfig()
            )
        ];

        $response = $soapClient->GetTransactionId($configuration);

        if (is_object($response) && $response->GetTransactionIdResult && $response->GetTransactionIdResult->success) {
            return $response->GetTransactionIdResult->sessionId;
        } else {
            throw new Exception($soapClient->__getLastResponse(), $response, $configuration);
        }
    }

    /**
     * @param string $transactionID
     * @return \Heystack\Payment\DPS\PXFusion\PaymentResponse
     * @throws \Heystack\Payment\DPS\PXFusion\Exception
     */
    public function checkTransaction($transactionID)
    {
        $soapClient = $this->getSoapClient();

        $configuration = [
            'username'      => $this->config[self::CONFIG_USERNAME],
            'password'      => $this->config[self::CONFIG_PASSWORD],
            'transactionId' => $transactionID
        ];

        $response = $soapClient->GetTransaction($configuration);

        if (!is_object($response) || !isset($response->GetTransactionResult)) {
            throw new Exception($soapClient->__getLastResponse(), $response, $configuration);
        }

        $result = $response->GetTransactionResult;
        $result->statusCode = $result->status;

        if (in_array($result->statusCode, $this->errorStatuses)) {
            $result->status = 'Error';
        } else {
            if ($result->statusCode === 0) {
                $result->status = 'Accepted';
            } elseif ($result->statusCode === 1) {
                $result->status = 'Declined';
            }
        }

        return new PaymentResponse(
            json_decode(
                json_encode((array) $result),
                true
            )
        );
    }

    /**
     * @param string $dpsTxnRef
     * @return array|bool|\Heystack\Payment\DPS\PXPost\PaymentResponse
     */
    public function completeTransaction($dpsTxnRef)
    {
        $this->setStage(self::STAGE_COMPLETE);

        if ($this->pxPostService instanceof PXPostService) {

            try {
                $this->pxPostService->setTxnType(PXPostService::TXN_TYPE_COMPLETE);
                $this->pxPostService->setAdditionalConfigByKey('DpsTxnRef', $dpsTxnRef);

                return $this->pxPostService->processComplete();
            } catch (\Exception $e) {
                return false;
            }

        }

        return false;
    }

    /**
     * Sets the stage of the Auth-Complete cycle
     * @param  string                 $stage
     * @throws ConfigurationException
     * @return void
     */
    public function setStage($stage)
    {
        if (
            $this->getType() == self::TYPE_AUTH_COMPLETE
            && in_array(
                $stage,
                [
                    self::STAGE_AUTH,
                    self::STAGE_COMPLETE
                ]
            )
        ) {
            $this->stage = $stage;
        } else {
            throw new ConfigurationException('Auth and Complete are the only supported stages for the Auth-Complete cycle');
        }
    }

    /**
     * @return string
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Get the amount for the transaction
     * @return string Amount
     */
    public function getAmount()
    {
        if ($this->getTxnType() == self::TXN_TYPE_AUTH) {

            return $this->formatAmount(new Money($this->authAmount, $this->currencyService->getActiveCurrency()));

        }

        return $this->formatAmount($this->transaction->getTotal());
    }

    /**
     * Set the amount to authorise when using Auth-Complete
     * @param int $authAmount an amount in cents.
     * @return void
     */
    public function setAuthAmount($authAmount)
    {
        $this->authAmount = $authAmount;
    }

    /**
     * Get the amount that should be authorised when using Auth-Complete
     * @return int
     */
    public function getAuthAmount()
    {
        return $this->authAmount;
    }

    /**
     * Set all status messages
     * @param array $statusMessages
     * @return void
     */
    public function setStatusMessages($statusMessages)
    {
        $this->statusMessages = $statusMessages;
    }

    /**
     * Get all status messages
     * @return array
     */
    public function getStatusMessages()
    {
        return $this->statusMessages;
    }

    /**
     * Set a particular status message by code
     * @param int $code
     * @param string $message
     * @return void
     */
    public function setStatusMessage($code, $message)
    {
        $this->statusMessages[$code] = $message;
    }

    /**
     * Get a particular status message by code
     * @param int $code
     * @return bool
     */
    public function getStatusMessage($code)
    {
        return isset($this->statusMessages[$code]) ? $this->statusMessages[$code] : false;
    }

    /**
     * @param  string                 $wsdl
     * @throws ConfigurationException
     * @return void
     */
    public function setWsdl($wsdl)
    {
        if (!\Director::is_absolute_url($wsdl)) {

            throw new ConfigurationException("Wsdl needs to be an absolute url");

        }

        $this->wsdl = $wsdl;
    }

    /**
     * Get the wsdl
     * @return string
     */
    public function getWsdl()
    {
        return ($this->getTestingMode() ? $this->testingServerUrl : $this->liveServerUrl) . $this->wsdl;
    }

    /**
     * Get the form action to be used on the form that accepts the credit card details
     * @return string
     */
    public function getFormAction()
    {
        return ($this->getTestingMode() ? $this->testingServerUrl : $this->liveServerUrl) . '/pxmi3/pxfusionauth';
    }
}
