<?php

namespace Heystack\Subsystem\Payment\Interfaces;

use Heystack\Subsystem\Core\Input\ProcessorInterface;

interface PaymentProcessorInterface extends ProcessorInterface
{
    public function getURL();
}