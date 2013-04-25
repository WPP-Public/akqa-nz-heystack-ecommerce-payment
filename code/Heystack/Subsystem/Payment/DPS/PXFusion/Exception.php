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

/**
 * Class Exception
 * @package Heystack\Subsystem\Payment\DPS\PXFusion
 */
class Exception extends \Exception
{
    /**
     * @param string     $lastResponse
     * @param int        $response
     * @param \Exception $configuration
     * @param null       $code
     * @param null       $previous
     */
    public function __construct($lastResponse, $response, $configuration, $code = null, $previous = null)
    {
        parent::__construct(
            $lastResponse . PHP_EOL . print_r($response, true) . print_r($configuration, true),
            $code,
            $previous
        );
    }
}
