<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Output namespace
 */
namespace Heystack\Subsystem\Payment\DPS\Output;

use Heystack\Subsystem\Core\Output\ProcessorInterface;

/**
 * Output Processor for DPS Payment
 *
 * Handles all output related to DPS Payments
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 *
 */
class Processor implements ProcessorInterface
{
    /**
     * Returns the identifier for this object
     * @return string
     */
    public function getIdentifier()
    {
        return 'dps';
    }

    /**
     * Method used to determine how to handle the output based on the InputProcessor's result
     * @param  \Controller     $controller
     * @param  type            $result
     * @return SS_HTTPResponse
     */
    public function process(\Controller $controller, $result = null)
    {
        if ($controller->isAjax()) {

            $response = $controller->getResponse();
            $response->setStatusCode(200);
            $response->addHeader('Content-Type', 'application/json');

            $response->setBody(json_encode($result));

            return $response;
        } else {
            $controller->redirectBack();
        }

        return null;
    }

}
