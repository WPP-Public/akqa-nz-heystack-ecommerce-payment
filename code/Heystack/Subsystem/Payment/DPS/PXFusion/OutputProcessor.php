<?php

namespace Heystack\Subsystem\Payment\DPS\PXFusion;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Output\ProcessorInterface;

/**
 * Class OutputProcessor
 * @package Heystack\Subsystem\Payment\DPS\PXFusion
 */
class OutputProcessor implements ProcessorInterface
{

    /**
     *
     */
    const IDENTIFIER = 'dps_fusion';

    /**
     * @var
     */
    protected $completeURL;

    /**
     * @var
     */
    protected $confirmationURL;

    /**
     * @var
     */
    protected $failureURL;

    /**
     * @param $completeURL
     * @param $confirmationURL
     * @param $failureURL
     */
    public function __construct($completeURL, $confirmationURL, $failureURL)
    {
        $this->completeURL = $completeURL;

        $this->confirmationURL = $confirmationURL;

        $this->failureURL = $failureURL;
    }
    /**
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * @param \Controller $controller
     * @param null        $result
     */
    public function process(\Controller $controller, $result = null)
    {

        if ($result['Success']) {

            if (isset($result['Complete']) && $result['Complete']) {

                \Director::redirect($this->completeURL);

                return;

            } else {

                \Director::redirect($this->confirmationURL);

                return;
            }

        }

        if (isset($result['CheckFailure']) && $result['CheckFailure']) {

            \Director::redirectBack();

        } else {

            \Director::redirect($this->failureURL);

        }

        return;
    }

}
