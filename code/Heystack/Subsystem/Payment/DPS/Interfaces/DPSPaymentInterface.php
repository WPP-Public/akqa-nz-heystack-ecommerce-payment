<?php

namespace Heystack\Subsystem\Payment\DPS\Interfaces;

use \Heystack\Subsystem\Payment\Interfaces\PaymentInterface;

interface DPSPaymentInterface extends PaymentInterface
{
    public function setTransactionReference($transactionReference);
    public function getTransactionReference();
    
    public function setAuthCode($authCode);
    public function getAuthCode();
    
}