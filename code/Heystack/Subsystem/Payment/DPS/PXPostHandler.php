<?php

namespace Heystack\Subsystem\Payment\DPS;

use Heystack\Subsystem\Payment\DPS\Interfaces\PXPostPaymentInterface;
use Heystack\Subsystem\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Subsystem\Payment\Events;

class PXPostHandler implements PaymentHandlerInterface
{
    use PaymentConfigTrait;
    
    const STATE_KEY = 'payment_handler';
    const CONFIG_KEY = 'configkey';
    const PAYMENT_DATA_KEY = 'paymentdatakey';
    
    const POST_USERNAME = 'PostUsername';
    const POST_PASSWORD = 'PostPassword';
    const GATEWAY_URL = 'GatewayURL';
    const MERCHANT_REFERENCE_PREFIX = 'MerchantReferencePrefix';
    
    const DEFAULT_GATEWAY_URL = 'https://sec.paymentexpress.com/pxpost.aspx';
    
    protected $paymentClass;
    protected $eventService;
    protected $currencyService;
    protected $transaction;
    
    protected $data = array();
    
    public function __construct(
            $paymentClass,
            EventDispatcherInterface $eventService, 
            TransactionInterface $transaction, 
            CurrencyServiceInterface $currencyService
    )
    {
        $this->paymentClass = $paymentClass;
        $this->eventService = $eventService;
        $this->transaction = $transaction;
        $this->currencyService = $currencyService;
    }
    
    protected function getRequiredConfigParameters()
    {
        return array(
            self::POST_USERNAME,
            self::POST_PASSWORD,
            self::MERCHANT_REFERENCE_PREFIX
        );
    }
    
    public function savePaymentData(array $data)
    {
        unset($data['url']);
        $this->data[self::PAYMENT_DATA_KEY] = $data;
        
        $this->eventService->dispatch(TransactionEvents::STORE);
    }
    
    protected function prepareDataForPayment()
    {
        $data = $this->data[self::PAYMENT_DATA_KEY];
        
        $data['PostUsername'] = $this->data[self::CONFIG_KEY][self::POST_USERNAME];
		$data['PostPassword'] = $this->data[self::CONFIG_KEY][self::POST_PASSWORD];
        
        return $this->checkPaymentData($data) ? $data : null;
    }
    
    protected function checkPaymentData($data)
    {
        $required = array(
            'Amount',
            'InputCurrency',
            'PostUsername',
            'PostPassword',
            'TxnType',
            'CardHolderName',
            'CardNumber',
            'Cvc2'
        );
        
        $missing = array_diff($required, array_keys($data));
        
        if(!count($missing)){
            return true;
        }else{
            throw new \Exception('The following required fields are missing: ' . implode(', ', $missing));
        }
        
        return false;
    }
    
    public function executePayment($transactionID)
    {
        $data = $this->prepareDataForPayment();
        
        $payment = new $this->paymentClass();
        if(! $payment instanceof PXPostPaymentInterface){
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
		foreach($data as $name => $value) {
			if($name == "Amount") {
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
		foreach($values as $xmlElement) {
			if($xmlElement['type'] == 'open') {
				if(array_key_exists('attributes', $xmlElement)) list($level[$xmlElement['level']], $extra) = array_values($xmlElement['attributes']);
				else $level[$xmlElement['level']] = $xmlElement['tag'];
			}
			else if ($xmlElement['type'] == 'complete') {
				$startLevel = 1;
				$phpArray = '$resultPhp';
				while($startLevel < $xmlElement['level']) $phpArray .= '[$level['. $startLevel++ .']]';
				$phpArray .= '[$xmlElement[\'tag\']] = array_key_exists(\'value\', $xmlElement)? $xmlElement[\'value\'] : null;';
				eval($phpArray);
			}
		}
		
		$responseFields = $resultPhp['TXN'];

		// 7) DPS Response Management
		if($responseFields['SUCCESS']) {
            $payment->setStatus('Success');
			if($authcode = $responseFields['1']['AUTHCODE']) $payment->setAuthCode ($authcode);
			if($dpsBillingID = $responseFields['1']['DPSBILLINGID']) $payment->setBillingID ($dpsBillingID);
			
			$dateSettlement = $responseFields['1']['DATESETTLEMENT'];
            $payment->setSettlementDate(substr($dateSettlement, 0, 4) ."-".substr($dateSettlement, 4, 2)."-".substr($dateSettlement, 6, 2));
		}
		else {
			$payment->setStatus('Failure');
		}
		if($transactionRef = $responseFields['DPSTXNREF']) $payment->setTransactionReference($transactionRef);
		if($helpText = $responseFields['HELPTEXT']) $payment->setHelpText($helpText);
		if($responseText = $responseFields['RESPONSETEXT']) $payment->setMessage($responseText);
		if($responseCode = $responseFields['RECO']) $payment->setResponseCode($responseCode);
        

		$payment->write();
        
        switch ($payment->getStatus()){
            case 'Failure':
                $this->eventService->dispatch(Events::FAILED);
                break;
            case 'Success':
                $this->eventService->dispatch(Events::SUCCESSFUL);
                break;
        }
    }
    
    
}