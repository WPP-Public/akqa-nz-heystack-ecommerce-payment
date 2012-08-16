<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Subsystem\Payment\DPS;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Heystack\Subsystem\Payment\DPS\Interfaces\PXPostPaymentInterface;
use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;
use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;
use Heystack\Subsystem\Payment\Events;
use Heystack\Subsystem\Payment\Events\PaymentEvent;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

/**
 * Contains the main logic for creating Payment objects as well as interfacing
 * with DPS's PXPost API
 *
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @package Ecommerce-Payment
 */
class PXPostHandler implements PaymentHandlerInterface
{
    use PaymentConfigTrait;

    /**
     * @todo remove this after transitioning to the Services constants for identifying services
     */
    const STATE_KEY = 'payment_handler';

    /**
     * Holds the key for storing configuration settings on the data array
     */
    const CONFIG_KEY = 'configkey';

    /**
     * Holds the key for storing payment data on the data array
     */
    const PAYMENT_DATA_KEY = 'paymentdatakey';

    /**
     * Holds the key for storing Post Username on the config second level array on the data array
     */
    const POST_USERNAME = 'PostUsername';

    /**
     * Holds the key for storing Post Password on the config second level array on the data array
     */
    const POST_PASSWORD = 'PostPassword';

    /**
     * Holds the key for storing the Gateway URL on the config second level array on the data array
     */
    const GATEWAY_URL = 'GatewayURL';

    /**
     * Holds the key for storing the Merchant Reference Prefix on the config second level array on the data array
     */
    const MERCHANT_REFERENCE_PREFIX = 'MerchantReferencePrefix';

    /**
     * Holds the default gateway url
     */
    const DEFAULT_GATEWAY_URL = 'https://sec.paymentexpress.com/pxpost.aspx';

    /**
     * Holds the payment class name to be used for creating Payment objects
     * @var string
     */
    protected $paymentClass;

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
     * Creates the PxPostHandler object
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
            self::POST_USERNAME,
            self::POST_PASSWORD,
            self::MERCHANT_REFERENCE_PREFIX
        );
    }

    /**
     * Saves the data that comes from the payment form submission for later use
     * @param array $data
     */
    public function savePaymentData(array $data)
    {
        unset($data['url']);
        $this->data[self::PAYMENT_DATA_KEY] = $data;

        $this->eventService->dispatch(TransactionEvents::STORE);
    }

    /**
     * Prepare the payment form submission data for use when executing the payment
     * @return array
     */
    protected function prepareDataForPayment()
    {
        $data = $this->data[self::PAYMENT_DATA_KEY];

        $data['PostUsername'] = $this->data[self::CONFIG_KEY][self::POST_USERNAME];
        $data['PostPassword'] = $this->data[self::CONFIG_KEY][self::POST_PASSWORD];
        $data['Amount'] = $this->transaction->getTotal();
        $data['InputCurrency'] = $this->transaction->getCurrencyCode();
        $data['TxnType'] = 'Purchase';

        return $this->checkPaymentData($data) ? $data : null;
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
        $required = array(
            'PostUsername',
            'PostPassword',
            'CardHolderName',
            'CardNumber',
            'Cvc2'
        );

        $missing = array_diff($required, array_keys($data));

        if (!count($missing)) {
            return true;
        } else {
            throw new \Exception('The following required fields are missing: ' . implode(', ', $missing));
        }

        return false;
    }

    /**
     * Execute the payment by creating the Payment object and contacting DPS to
     * handle the payment.
     * @param  type       $transactionID
     * @throws \Exception
     */
    public function executePayment($transactionID)
    {
        $data = $this->prepareDataForPayment();

        $payment = new $this->paymentClass();
        if (! $payment instanceof PXPostPaymentInterface) {
            throw new \Exception($this->paymentClass . ' must implement PXPostPaymentInterface');
        }

        $payment->setAmount($data['Amount']);
        $payment->setTransactionType($data['TxnType']);
        $payment->setIP($_SERVER['REMOTE_ADDR']);
        $payment->setTransactionID($transactionID);
        $payment->setCurrencyCode($data['InputCurrency']);
        $payment->setMerchantReference($this->data[self::CONFIG_KEY][self::MERCHANT_REFERENCE_PREFIX] . ' Transaction ID:' . $transactionID);

        // 1) Transaction Creation
        $transaction = "<Txn>";
        foreach ($data as $name => $value) {
            if ($name == "Amount") {
                $value = number_format($value, 2, '.', '');
            }
            $transaction .= "<$name>$value</$name>";
        }
        $transaction .= "</Txn>";

        // 2) CURL Creation
        $gatewayURL = isset($this->data[self::CONFIG_KEY][self::GATEWAY_URL]) ? $this->data[self::CONFIG_KEY][self::GATEWAY_URL] : self::DEFAULT_GATEWAY_URL;
        $clientURL = curl_init();
        curl_setopt($clientURL, CURLOPT_URL, $gatewayURL);
        curl_setopt($clientURL, CURLOPT_POST, 1);
        curl_setopt($clientURL, CURLOPT_POSTFIELDS, $transaction);
        curl_setopt($clientURL, CURLOPT_RETURNTRANSFER, 1);
//		curl_setopt($clientURL, CURLOPT_SSL_VERIFYPEER, 0); //Needs to be included if no *.crt is available to verify SSL certificates
        curl_setopt($clientURL, CURLOPT_SSLVERSION, 3);


        // 3) CURL Execution

        $resultXml = curl_exec($clientURL);
        $payment->setXMLResponse($resultXml);
        // 4) CURL Closing
        curl_close ($clientURL);

        // 5) XML Parser Creation
        $xmlParser = xml_parser_create();
        $values = null;
        $indexes = null;
        xml_parse_into_struct($xmlParser, $resultXml, $values, $indexes);
        xml_parser_free($xmlParser);

        // 6) XML Result Parsed In A PHP Array
        $resultPhp = array();
        $level = array();
        foreach ($values as $xmlElement) {
            if ($xmlElement['type'] == 'open') {
                if(array_key_exists('attributes', $xmlElement)) list($level[$xmlElement['level']], $extra) = array_values($xmlElement['attributes']);
                else $level[$xmlElement['level']] = $xmlElement['tag'];
            } elseif ($xmlElement['type'] == 'complete') {
                $startLevel = 1;
                $phpArray = '$resultPhp';
                while($startLevel < $xmlElement['level']) $phpArray .= '[$level['. $startLevel++ .']]';
                $phpArray .= '[$xmlElement[\'tag\']] = array_key_exists(\'value\', $xmlElement)? $xmlElement[\'value\'] : null;';
                eval($phpArray);
            }
        }

        $responseFields = $resultPhp['TXN'];

        // 7) DPS Response Management
        if ($responseFields['SUCCESS']) {
            $payment->setStatus('Success');
            if($authcode = $responseFields['1']['AUTHCODE']) $payment->setAuthCode($authcode);
            if($dpsBillingID = $responseFields['1']['DPSBILLINGID']) $payment->setBillingID($dpsBillingID);

            $dateSettlement = $responseFields['1']['DATESETTLEMENT'];
            $payment->setSettlementDate(substr($dateSettlement, 0, 4) ."-".substr($dateSettlement, 4, 2)."-".substr($dateSettlement, 6, 2));
        } else {
            $payment->setStatus('Failure');
        }
        if($transactionRef = $responseFields['DPSTXNREF']) $payment->setTransactionReference($transactionRef);
        if($helpText = $responseFields['HELPTEXT']) $payment->setHelpText($helpText);
        if($responseText = $responseFields['RESPONSETEXT']) $payment->setMessage($responseText);
        if($responseCode = $responseFields['RECO']) $payment->setResponseCode($responseCode);

        // add the transaction ID to the payment for later events
        $payment->setTransactionID($transactionID);

        if ($responseFields['SUCCESS']) {

            $this->eventService->dispatch(Events::SUCCESSFUL, new PaymentEvent($payment));

        } else {

            $this->eventService->dispatch(Events::FAILED, new PaymentEvent($payment));

        }

        return $payment;
    }

}
