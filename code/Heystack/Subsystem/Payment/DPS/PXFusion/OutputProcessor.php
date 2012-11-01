<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Output\ProcessorInterface;

class OutputProcessor implements ProcessorInterface
{

    const IDENTIFIER = 'dps_fusion';
    
    protected $completeURL;
    
    protected $confirmationURL;
    
    protected $failureURL;

    public function __construct($completeURL, $confirmationURL, $failureURL)
    {
        $this->completeURL = $completeURL;
        
        $this->confirmationURL = $confirmationURL;
        
        $this->failureURL = $failureURL;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function process(\Controller $controller, $result = null)
    {
        
        if ($result['Success']) {
            
            if ($result['Complete']) {
                
                \Director::redirect($this->completeURL);
                
                return;
                
            } else {
                
                \Director::redirect($this->confirmationURL);
                
                return;
            }

        }
        
        if ($result['CheckFailure']) {
            
            \Director::redirectBack();
            
        } else {
            
            \Director::redirect($this->failureURL);
            
        }
        
        
        
        return;
    }

}
