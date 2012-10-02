<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * DPS namespace
 */
namespace Heystack\Subsystem\Payment\DPS\PXFusion;

class Exception extends \Exception
{

    public function __construct($lastResponse, $response, $configuration, $code = null, $previous = null)
    {

        parent::__construct($lastResponse . PHP_EOL . print_r($response, true) . print_r($configuration, true), $code, $previous);

    }

}