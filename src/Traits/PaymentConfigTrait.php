<?php
/**
 * This file is part of the Ecommerce-Payment package
 *
 * @package Ecommerce-Payment
 */

/**
 * Traits namespace
 */
namespace Heystack\Payment\Traits;

use Heystack\Core\Exception\ConfigurationException;

/**
 * Provides an implementation of setting and getting the configuration for use on a PaymentHandler class
 *
 * @copyright  Heyday
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 */
trait PaymentConfigTrait
{
    /**
     * Stores config for payment services
     * @var array
     */
    protected $config = [];

    /**
     * Any additional information to be passed to dps
     * @var array
     */
    protected $additionalConfig = [];

    /**
     * Holds data submitted by the user
     * @var array
     */
    protected $userConfig = [];

    /**
     * Returns an array of required parameters used in setConfig
     * @return array
     */
    abstract protected function getRequiredConfig();

    /**
     * Returns an array of allowed config parameters
     * @return array
     */
    abstract protected function getAllowedConfig();

    /**
     * Validates config
     * @param array $config
     * @return array
     */
    abstract protected function validateConfig(array $config);

    /**
     * Returns an array of required additional config params
     * @return array
     */
    abstract protected function getRequiredAdditionalConfig();

    /**
     * Returns an array of allowed additional config params
     * @return array
     */
    abstract protected function getAllowedAdditionalConfig();

    /**
     * Validates additional config
     * @param array $config
     * @return array
     */
    abstract protected function validateAdditionalConfig(array $config);

    /**
     * Returns an array of required parameters used in setConfig
     * @return array
     */
    abstract protected function getRequiredUserConfig();

    /**
     * Returns an array of allowed config parameters
     * @return array
     */
    abstract protected function getAllowedUserConfig();

    /**
     * Validates config
     * @param array $config
     * @return array
     */
    abstract protected function validateUserConfig(array $config);

    /**
     * @param bool $exceptionOnError
     * @return array
     */
    public function checkAll($exceptionOnError = false)
    {
        return array_merge(
            $this->checkConfig($this->getConfig(), $exceptionOnError),
            $this->checkAdditionalConfig($this->getAdditionalConfig(), $exceptionOnError),
            $this->checkUserConfig($this->getUserConfig(), $exceptionOnError)
        );
    }

    /**
     * @param $config
     * @param bool $exceptionOnError
     * @return array
     */
    public function checkConfig($config, $exceptionOnError = false)
    {
        return $this->checkConfigHelper(
            $config,
            $this->getRequiredConfig(),
            $this->getAllowedConfig(),
            [$this, 'validateConfig'],
            $exceptionOnError
        );
    }

    /**
     * @param $config
     * @param bool $exceptionOnError
     * @return array
     */
    public function checkAdditionalConfig($config, $exceptionOnError = false)
    {
        return $this->checkConfigHelper(
            $config,
            $this->getRequiredAdditionalConfig(),
            $this->getAllowedAdditionalConfig(),
            [$this, 'validateAdditionalConfig'],
            $exceptionOnError
        );
    }

    /**
     * @param $config
     * @param bool $exceptionOnError
     * @return array
     */
    public function checkUserConfig($config, $exceptionOnError = false)
    {
        return $this->checkConfigHelper(
            $config,
            $this->getRequiredUserConfig(),
            $this->getAllowedUserConfig(),
            [$this, 'validateUserConfig'],
            $exceptionOnError
        );
    }

    /**
     * @param array $config
     * @param array $required
     * @param array $allowed
     * @param callable $extraValidation
     * @param bool $exceptionOnError
     * @return array
     * @throws \Heystack\Core\Exception\ConfigurationException
     */
    protected function checkConfigHelper(
        array $config,
        array $required,
        array $allowed,
        callable $extraValidation = null,
        $exceptionOnError = false
    ) {
        $errors = [];

        foreach (array_diff(array_keys($config), $allowed) as $notAllowed) {
            $errors[] = $this->errorNotAllowed($notAllowed);
        }

        foreach (array_diff($required, array_keys($config)) as $isRequired) {
            $errors[] = $this->errorRequired($isRequired);
        }

        if (is_callable($extraValidation)) {

            foreach ($extraValidation($config) as $error) {
                $errors[] = $error;
            }

        }

        if ($this->hasErrors($errors) && $exceptionOnError) {
            throw new ConfigurationException(implode(', ', $errors));
        }

        return $errors;
    }

    /**
     * @param $notAllowed
     * @return string
     */
    protected function errorNotAllowed($notAllowed)
    {
        return "The config option '$notAllowed' is not allowed";
    }

    /**
     * @param $isRequired
     * @return string
     */
    protected function errorRequired($isRequired)
    {
        return "The config option '$isRequired' is required";
    }

    /**
     * @param $errors
     * @return bool
     */
    protected function hasErrors($errors)
    {
        return count($errors) !== 0;
    }

    /**
     * Sets an array of config parameters onto the data array.
     * Checks to see if all the required parameters are present.
     * @param array $config
     * @param bool $exceptionOnError
     * @return array|bool
     */
    public function setConfig(array $config, $exceptionOnError = false)
    {
        $errors = $this->checkConfig($config, $exceptionOnError);

        if ($this->hasErrors($errors)) {
            return $errors;
        }

        $this->config = $config;

        return true;
    }

    /**
     * Set the additional configuration
     * @param array $config
     * @param bool $exceptionOnError
     * @return array|bool
     */
    public function setAdditionalConfig(array $config, $exceptionOnError = false)
    {
        $errors = $this->checkAdditionalConfig($config, $exceptionOnError);

        if (count($errors) !== 0) {
            return $errors;
        }

        $this->additionalConfig = $config;

        return true;
    }

    /**
     * @param array $config
     * @param bool $exceptionOnError
     * @return array|bool
     */
    public function setUserConfig(array $config, $exceptionOnError = false)
    {
        $errors = $this->checkUserConfig($config, $exceptionOnError);

        if (count($errors) !== 0) {
            return $errors;
        }

        $this->userConfig = $config;

        return true;
    }

    /**
     * @param $key
     * @param $value
     * @param bool $exceptionOnError
     * @return array|bool
     */
    public function setConfigByKey($key, $value, $exceptionOnError = false)
    {
        return $this->setConfig(array_merge($this->config, [
            $key => $value
        ]), $exceptionOnError);
    }

    /**
     * @param $key
     * @param $value
     * @param bool $exceptionOnError
     * @return array|bool
     */
    public function setAdditionalConfigByKey($key, $value, $exceptionOnError = false)
    {
        return $this->setAdditionalConfig(array_merge($this->additionalConfig, [
            $key => $value
        ]), $exceptionOnError);
    }

    /**
     * @param $key
     * @param $value
     * @param bool $exceptionOnError
     * @return array|bool
     */
    public function setUserConfigByKey($key, $value, $exceptionOnError = false)
    {
        return $this->setUserConfig(array_merge($this->userConfig, [
            $key => $value
        ]), $exceptionOnError);
    }

    /**
     * Retrieves the configuration array
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getAdditionalConfig()
    {
        return $this->additionalConfig;
    }

    /**
     * @return array
     */
    public function getUserConfig()
    {
        return $this->userConfig;
    }

    /**
     * @param $key
     * @return bool
     */
    public function getConfigByKey($key)
    {
        return isset($this->config[$key]) ? $this->config[$key] : false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function getAdditionalConfigByKey($key)
    {
        return isset($this->additionalConfig[$key]) ? $this->additionalConfig[$key] : false;
    }

    /**
     * @param $key
     * @return bool
     */
    public function getUserConfigByKey($key)
    {
        return isset($this->userConfig[$key]) ? $this->userConfig[$key] : false;
    }
}
