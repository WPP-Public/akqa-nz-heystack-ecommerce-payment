<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Output\ProcessorInterface;

class OutputProcessor implements ProcessorInterface
{

    const IDENTIFIER = 'dps_fusion';

    /**
     * Holds the payment handler
     * @var \Heystack\Subsystem\Payment\DPS\PXFusion\PaymentHandlerInterface
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

    public function process(\Controller $controller, $result = null)
    {
        
    }

}