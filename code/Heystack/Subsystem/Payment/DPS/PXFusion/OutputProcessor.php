<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Output\ProcessorInterface;

class OutputProcessor implements ProcessorInterface
{

    const IDENTIFIER = 'dps_fusion';

    /**
     * Holds the payment handler
     * @var \Heystack\Subsystem\Payment\DPS\PXFusion\PaymentServiceInterface
     */
    protected $paymentService;

    public function __construct(PaymentServiceInterface $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    public function process(\Controller $controller, $result = null)
    {
        
        if ($result['Success']) {
            
            if ($result['Complete']) {
                
                \Director::redirect('checkout/thankyou');
                
                return;
                
            } else {
                
                \Director::redirect('checkout/confirm');
                
                return;
            }

        }
        
        \Director::redirectBack();
        
        return;
    }

}
