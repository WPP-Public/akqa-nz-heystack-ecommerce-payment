<?php

namespace Heystack\Subsystem\Payment\DPS\Input;

use Heystack\Subsystem\Payment\Interfaces\PaymentProcessorInterface;

class Processor implements PaymentProcessorInterface
{
    
    public function getIdentifier()
    {
        return 'dps';
    }
    
    public function getURL()
    {        
        return 'http://' . $_SERVER['HTTP_HOST'] . '/' . \EcommerceInputController::$url_segment . '/process/' . $this->getIdentifier();
    }
    
    public function process(\SS_HTTPRequest $request)
    {
        return array('success' => 'true');
    }
}
