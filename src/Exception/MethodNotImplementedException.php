<?php

namespace Heystack\Payment\Exception;

/**
 * Class MethodNotImplemented
 * @package Heystack\Payment\Exception
 */
class MethodNotImplementedException extends \Exception
{
    /**
     * @param string $methodName The name of the method
     */
    public function __construct($methodName)
    {
        parent::__construct(sprintf('The %s() is not implemented.', $methodName));
    }
} 