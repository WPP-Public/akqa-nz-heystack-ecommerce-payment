<?php

/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Payment
 */
/**
 * Heystack\Payment\DPS namespace
 */

namespace Heystack\Payment\DPS\PXPay;

use Heystack\Payment\DPS\Service as BaseService;
use Heystack\Payment\Exception\MethodNotImplementedException;

/**
 * PXPayHandler allows for payments to be handled through the PXPay interface
 * for DPS payments.
 *
 * @todo Finish this, it is currently in a non-working state.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Heystack
 *
 */
class Service extends BaseService
{
    /**
     * Returns an array of required parameters used in setConfig
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function getRequiredConfig()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Returns an array of allowed config parameters
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function getAllowedConfig()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Validates config
     * @param array $config
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function validateConfig(array $config)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Returns an array of required additional config params
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function getRequiredAdditionalConfig()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Returns an array of allowed additional config params
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function getAllowedAdditionalConfig()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Validates additional config
     * @param array $config
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function validateAdditionalConfig(array $config)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Returns an array of required parameters used in setConfig
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function getRequiredUserConfig()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Returns an array of allowed config parameters
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function getAllowedUserConfig()
    {
        throw new MethodNotImplementedException(__METHOD__);
    }

    /**
     * Validates config
     * @param array $config
     * @throws \Heystack\Payment\Exception\MethodNotImplementedException
     * @return array
     */
    protected function validateUserConfig(array $config)
    {
        throw new MethodNotImplementedException(__METHOD__);
    }
}
