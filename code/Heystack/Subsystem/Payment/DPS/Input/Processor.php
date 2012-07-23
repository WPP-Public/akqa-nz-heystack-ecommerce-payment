<?php

namespace Heystack\Subsystem\Payment\DPS\Input;

use Heystack\Subsystem\Core\Input\ProcessorInterface;
use Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface;

class Processor implements ProcessorInterface
{
    protected $paymentHandler;
    
    public function __construct(PaymentHandlerInterface $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }
    
    public function getIdentifier()
    {
        return 'dps';
    }
    
    public function process(\SS_HTTPRequest $request)
    {
        $data = $request->requestVars();
        
        $this->paymentHandler->savePaymentData($data);
        
        return array('success' => 'true');
    }
    
}
