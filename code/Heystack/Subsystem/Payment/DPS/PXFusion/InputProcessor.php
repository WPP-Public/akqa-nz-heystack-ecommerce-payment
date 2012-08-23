<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Input\ProcessorInterface;

class InputProcessor implements ProcessorInterface
{

    const IDENTIFIER = 'dps_fusion';

    /**
     * Holds the payment handler
     * @var \Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface
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

    public function process(\SS_HTTPRequest $request)
    {

        $httpMethod = $request->httpMethod();

        if ($httpMethod == 'POST' && $request->param('ID') == 'complete') {

            //Complete the transaction
//            $this->paymentService->completeTransaction();

        } elseif ($httpMethod == 'GET' && $request->param('ID') == 'return') {

            $this->paymentService->checkTransaction($request->getVar('sessionid'));

        } elseif ($httpMethod == 'GET' && $request->param('ID') == 'auth') {

            $this->paymentService->checkTransaction($request->getVar('sessionid'));

        }

    }

}
