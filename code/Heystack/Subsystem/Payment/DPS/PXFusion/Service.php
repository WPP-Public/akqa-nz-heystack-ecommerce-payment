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

use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;
use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;

use Heystack\Subsystem\Core\Exception\ConfigurationException;

/**
 *
 *
 * @copyright  Heyday
 * @package Ecommerce-Payment
 */
class Service implements PaymentHandlerInterface
{

    use PaymentConfigTrait;

    /**
     * Holds the key for storing configuration settings on the data array
     */
    const CONFIG_KEY = 'configkey';

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
            'Type', //Auth-Complete, Purchase
            'Username',
            'Password',
            'Wsdl'
        );
    }
    
    protected function validateConfig($config)
    {
        
        if (!in_array($config['Type'], array(
            'Auth-Complete',
            'Purchase'
        ))) {
            
            throw new ConfigurationException("{$config['Type']} is not a valid Type for this payment handler");
            
        }
        
        if (!\Director::is_absolute_url($config['Wsdl'])) {
            
            throw new ConfigurationException("Wsdl needs to be an absolute url");
            
        }
        
    }
    
    public function getType()
    {
        
        return isset($this->data[self::CONFIG_KEY]['Type']) ? $this->data[self::CONFIG_KEY]['Type'] : false;
        
    }
    
    public function setType($type)
    {
        
        $this->data[self::CONFIG_KEY]['Type'] = $type;
        
        $this->validateConfig($this->data[self::CONFIG_KEY]);
        
    }
    
    public function getReturnUrl()
    {
        
        switch ($this->data[self::CONFIG_KEY]['Type']) {
            
            case 'Auth-Complete':
                return \Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/auth');
            case 'Purchase':
                return \Director::absoluteURL(\EcommerceInputController::$url_segment . '/process/' . InputProcessor::IDENTIFIER . '/purchase');
            
        }
        
    }
    
    protected function getTxnType()
    {
        
        switch ($this->data[self::CONFIG_KEY]['Type']) {
            
            case 'Auth-Complete':
                return 'Auth';
            case 'Purchase':
                return 'Purchase';
            
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
        
        $soapClient = new \SoapClient($this->wsdl, array('soap_version' => SOAP_1_1));

    }

}
