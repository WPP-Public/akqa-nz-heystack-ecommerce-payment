<?php

namespace Heystack\Subsystem\Payment\Test;

use Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionInterface;

class TestTransaction implements TransactionInterface
{

    public function addModifier(\Heystack\Subsystem\Ecommerce\Transaction\Interfaces\TransactionModifierInterface $modifier)
    {
        
    }

    public function getCollator()
    {
        
    }

    public function getCurrencyCode()
    {
        
    }

    public function getModifier($identifier)
    {
        
    }

    public function getModifiers()
    {
        
    }

    public function getModifiersByType($type)
    {
        
    }

    public function getTotal()
    {
        
    }

    public function getTotalWithExclusions(array $exclude)
    {
        
    }

    public function setCurrencyCode($currencyCode)
    {
        
    }

    public function updateTotal()
    {
        
    }

}