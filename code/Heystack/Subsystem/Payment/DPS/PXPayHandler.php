<?php

namespace Heystack\Subsystem\Payment\DPS;

use Heystack\Subsystem\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;
use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

class PXPayHandler implements PaymentHandlerInterface
{
    use PaymentConfigTrait;
    
    const STATE_KEY = 'payment_handler';
    const CONFIG_KEY = 'configkey';
    const PAYMENT_DATA_KEY = 'paymentdatakey';
    
    const PXPAY_USER_ID = 'PxPayUserId';
    const PXPAY_KEY = 'PxPayKey';
    const TRANSACTION_TYPE = 'TransactionType';
    const GATEWAY_URL = 'GatewayURL';
    const SUCCESS_URL = 'SuccessURL';
    const FAILURE_URL = 'FailureURL';
    
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
            self::PXPAY_USER_ID,
            self::PXPAY_KEY,
            self::TRANSACTION_TYPE,
            self::GATEWAY_URL,
            self::SUCCESS_URL,
            self::FAILURE_URL,
        );
    }
    
    public function savePaymentData(array $data)
    {
        $this->data[self::PAYMENT_DATA_KEY] = $data;
        
        $this->eventService->dispatch(TransactionEvents::STORE);
    }
    
    protected function executePayment($transactionID)
    {
        $request = $this->preparePxPayRequest();
        
        $pxpay = new \PxPay(
                $this->data[self::CONFIG_KEY][self::GATEWAY_URL],
                $this->data[self::CONFIG_KEY][self::PXPAY_USER_ID], 
                $this->data[self::CONFIG_KEY][self::PXPAY_KEY]);
        
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
        $request->setUrlFail($this->data[self::CONFIG_KEY][self::FAILURE_URL]);
        $request->setUrlSuccess($this->data[self::CONFIG_KEY][self::SUCCESS_URL]);
        
        return $request;
    }
}