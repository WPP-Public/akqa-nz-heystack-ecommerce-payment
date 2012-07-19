<?php

namespace Heystack\Subsystem\Payment\DPS;

use Heystack\Subsystem\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;
use Heystack\Subsystem\Payment\Interfaces\PaymentProcessorInterface;

class DPSHandler implements PaymentHandlerInterface
{
    use PaymentConfigTrait;
    
    const STATE_KEY = 'dps_handler';
    const CONFIG_KEY = 'configkey';
    
    const PAYMENT_TYPE = 'PaymentType';
    const PAYMENT_IDENTIFIER = 'PaymentIdentifier';
    const PASSCODE = 'Passcode';
    const TRANSACTION_TYPE = 'TransactionType';
    const GATEWAY_URL = 'GatewayURL';
    
    protected $eventService;
    protected $currencyService;
    protected $transaction;
    protected $paymentProcessor;
    
    protected $data = array();
    
    public function __construct(
            EventDispatcherInterface $eventService, 
            TransactionInterface $transaction, 
            CurrencyServiceInterface $currencyService,
            PaymentProcessorInterface $paymentProcessor
            )
    {
        $this->eventService = $eventService;
        $this->transaction = $transaction;
        $this->currencyService = $currencyService;
        $this->paymentProcessor = $paymentProcessor;
    }
    
    protected function getRequiredConfigParameters()
    {
        return array(
            self::PAYMENT_TYPE,
            self::PAYMENT_IDENTIFIER,
            self::PASSCODE,
            self::TRANSACTION_TYPE
        );
    }
    
    public function executePayment()
    {
        if( isset($this->data[self::CONFIG_KEY][self::PAYMENT_TYPE]) && in_array($this->data[self::CONFIG_KEY][self::PAYMENT_TYPE], array('PXPOST','PXPAY'))){
            switch($this->data[self::CONFIG_KEY][self::PAYMENT_TYPE]){
                case 'PXPOST':
                    break;
                case 'PXPAY':
                    $this->executeDPSHostedPayment();
                    break;
            }
        }else{
            throw new \Exception('Payment Type not configured correctly');
        }
    }
    
    protected function executeDPSHostedPayment()
    {
        $request = $this->preparePxPayRequest();
        
        $pxpay = new \PxPay(
                $this->data[self::CONFIG_KEY][self::GATEWAY_URL],
                $this->data[self::CONFIG_KEY][self::PAYMENT_IDENTIFIER], 
                $this->data[self::CONFIG_KEY][self::PASSCODE]);
        
        $request_string = $pxpay->makeRequest($request);
        $response = new \MifMessage($request_string);
        $valid = $response->get_attribute("valid");
        if($valid){
			// MifMessage was clobbering ampersands on some environments; SimpleXMLElement is more robust
	        $xml = new \SimpleXMLElement($request_string);
	        $urls = $xml->xpath('//URI');     
	        $url = $urls[0].'';
            
            header("Location: ".$url);
            die;
            
		}else{            
            throw new \Exception("Invalid Request String");
		}
    }
    
    protected function preparePxPayRequest()
    {
        $request = new \PxPayRequest();
        $request->setAmountInput($this->transaction->getTotal());
        $request->setInputCurrency($this->currencyService->getActiveCurrency()->getCurrencyCode());
        $request->setMerchantReference($this->transaction->getMerchantReference());
        $request->setTxnType($this->data[self::CONFIG_KEY][self::TRANSACTION_TYPE]);
        $request->setUrlFail($this->paymentProcessor->getURL());
        $request->setUrlSuccess($this->paymentProcessor->getURL());
        
        return $request;
    }
    
    
}