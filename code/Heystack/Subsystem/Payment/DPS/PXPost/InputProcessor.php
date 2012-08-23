<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Input namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXPost;

use Heystack\Subsystem\Core\Input\ProcessorInterface;
use Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface;

/**
 * Handles DPS specific input
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Core
 */
class InputProcessor implements ProcessorInterface
{
    /**
     * Holds the payment handler
     * @var \Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface
     */
    protected $paymentHandler;

    /**
     * Creates the processor object
     * @param \Heystack\Subsystem\Payment\Interfaces\PaymentServiceInterface $paymentHandler
     */
    public function __construct(PaymentServiceInterface $paymentHandler)
    {
        $this->paymentHandler = $paymentHandler;
    }

    /**
     * Returns an identifier used for routing to this processor
     * @return string
     */
    public function getIdentifier()
    {
        return 'dps_post';
    }

    /**
     * Processes the request and tells the payment handler what to do
     * @param  \SS_HTTPRequest $request
     * @return array
     */
    public function process(\SS_HTTPRequest $request)
    {
        $data = \Convert::raw2sql($request->requestVars());

        $this->paymentHandler->savePaymentData($data);

        return array('success' => 'true');
    }

}