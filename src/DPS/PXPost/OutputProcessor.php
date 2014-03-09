<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Output namespace
 */
namespace Heystack\Payment\DPS\PXPost;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Output\ProcessorInterface;

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
class OutputProcessor implements ProcessorInterface
{
    /**
     * Returns the identifier for this object
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier('dps_post');
    }

    /**
     * Method used to determine how to handle the output based on the InputProcessor's result
     * @param  \Controller     $controller
     * @param  type            $result
     * @return \SS_HTTPResponse
     */
    public function process(\Controller $controller, $result = null)
    {
        if ($controller->getRequest()->isAjax()) {

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
