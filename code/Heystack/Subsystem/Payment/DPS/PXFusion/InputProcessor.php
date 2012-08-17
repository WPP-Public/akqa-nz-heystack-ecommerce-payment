<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Input\ProcessorInterface;

class InputProcessor implements ProcessorInterface
{
    
    const IDENTIFIER = 'dps_fusion';

    /**
     * Holds the payment handler
     * @var \Heystack\Subsystem\Payment\Interfaces\PaymentHandlerInterface
     */
    protected $paymentHandler;

    function __construct(PaymentHandlerInterface $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function process(\SS_HTTPRequest $request)
    {
        
        $httpMethod = $request->httpMethod();
        
        if ($httpMethod == 'POST' && $request->param('ID') == 'complete') {
            
            
            
        } elseif ($httpMethod == 'GET' && $request->param('ID') == 'return') {
            
//            $this->paymentHandler->
            
        }
        
    }

}