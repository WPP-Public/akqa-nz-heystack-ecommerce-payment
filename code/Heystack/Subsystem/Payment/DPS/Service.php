<?php

namespace Heystack\Subsystem\Payment\DPS;

use Heystack\Subsystem\Payment\Traits\PaymentConfigTrait;

use Heystack\Subsystem\Core\Exception\ConfigurationException;

abstract class Service
{

    use PaymentConfigTrait;

    /**
     * List of currencies supported by DPS
     * @var array
     */
    protected $supportedCurrencies = array(
        'CAD', 'CHF', 'DKK', 'EUR',
        'FRF', 'GBP', 'HKD', 'JPY',
        'NZD', 'SGD', 'THB', 'USD',
        'ZAR', 'AUD', 'WST', 'VUV',
        'TOP', 'SBD', 'PGK', 'MYR',
        'KWD', 'FJD'
    );
    
    /**
     * List of currencies which don't have cents
     * @var array
     */
    protected $currenciesWithoutCents = array(
        'JPY'
    );

    /**
     * If testing last request data is needed form soap calls thi should be set to true
     * @var bool
     */
    protected $testingMode = false;

    abstract public function getTransaction();

    /**
     * Set the testing mode
     * @param boolean $testingMode
     */
    public function setTestingMode($testingMode)
    {
        $this->testingMode = $testingMode;
    }

    /**
     * Get the testing mode
     * @return boolean
     */
    public function getTestingMode()
    {
        return $this->testingMode;
    }

    /**
     * Returns the currency code.
     * @return mixed
     * @throws ConfigurationException
     */
    protected function getCurrencyCode()
    {
        $currencyCode = $this->getTransaction()->getCurrencyCode();

        if (!in_array($currencyCode, $this->supportedCurrencies)) {

            throw new ConfigurationException("The currency $currencyCode is not supported by DPS");

        }

        return $currencyCode;
    }
    
    protected function responseFromErrors($errors = null) {
        
        die();
        
    }

}