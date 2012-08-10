<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Traits namespace
 */
namespace Heystack\Subsystem\Payment\Traits;

/**
 * Provides an implementation of setting and getting the configuration for use on a PaymentHandler class
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
trait PaymentConfigTrait
{
    /**
     * Defines an array of required parameters used in setConfig
     * @return array
     */
    abstract protected function getRequiredConfigParameters();

    /**
     * Sets an array of config parameters onto the data array.
     * Checks to see if all the required parameters are present.
     * @param  array      $config
     * @throws \Exception
     */
    public function setConfig(array $config)
    {
        $missing = array_diff($this->getRequiredConfigParameters(), array_keys($config));

        if (!count($missing)) {
            foreach ($config as $key => $value) {
                $this->data[self::CONFIG_KEY][$key] = $value;
            }
        } else {
            throw new \Exception('The following settings are missing: ' . implode(', ', $missing));
        }
    }

    /**
     * Retrieves the configuration array
     * @return array
     */
    public function getConfig()
    {
        return isset($this->data[self::CONFIG_KEY]) ? $this->data[self::CONFIG_KEY] : null;
    }
}
