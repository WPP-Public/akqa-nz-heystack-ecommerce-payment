<?php

namespace Heystack\Payment\DPS;

use Heystack\Core\Exception\ConfigurationException;
use Heystack\Ecommerce\Currency\Traits\HasCurrencyServiceTrait;
use Heystack\Payment\Traits\PaymentConfigTrait;
use SebastianBergmann\Money\Money;

abstract class Service
{
    use HasCurrencyServiceTrait;
    use PaymentConfigTrait;

    /**
     * List of currencies supported by DPS
     * @var array
     */
    protected $supportedCurrencies = [
        'CAD', 'CHF', 'DKK', 'EUR',
        'FRF', 'GBP', 'HKD', 'JPY',
        'NZD', 'SGD', 'THB', 'USD',
        'ZAR', 'AUD', 'WST', 'VUV',
        'TOP', 'SBD', 'PGK', 'MYR',
        'KWD', 'FJD'
    ];

    /**
     * If testing last request data is needed form soap calls thi should be set to true
     * @var bool
     */
    protected $testingMode = false;

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
        $currencyCode = $this->currencyService->getActiveCurrencyCode();

        if (!in_array($currencyCode, $this->supportedCurrencies)) {

            throw new ConfigurationException(
                sprintf("The currency '%s' is not supported by DPS", $currencyCode)
            );

        }

        return $currencyCode;
    }

    protected function responseFromErrors($errors = null)
    {
        die();
    }

    /**
     * Returns the formatted payment amount
     * @param \SebastianBergmann\Money\Money
     * @return string
     */
    protected function formatAmount(Money $amount)
    {
        $currency = $amount->getCurrency();
        $subunit = $currency->getSubUnit();
        $fractionDigits = $currency->getDefaultFractionDigits();

        return number_format($amount->getAmount() / $subunit, $fractionDigits, '.', '');
    }
}
