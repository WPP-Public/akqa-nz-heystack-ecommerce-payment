<?php

namespace Heystack\Payment\DPS\PXFusion;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Output\ProcessorInterface;

/**
 * Class OutputProcessor
 * @package Heystack\Payment\DPS\PXFusion
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
     * @param string $completeURL
     * @param string $confirmationURL
     * @param string $failureURL
     */
    public function __construct($completeURL, $confirmationURL, $failureURL)
    {
        $this->completeURL = $completeURL;
        $this->confirmationURL = $confirmationURL;
        $this->failureURL = $failureURL;
    }

    /**
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * @param \Controller $controller
     * @param mixed|void        $result
     * @return void
     */
    public function process(\Controller $controller, $result = null)
    {

        if ($result['Success']) {

            if (isset($result['Complete']) && $result['Complete']) {

                $controller->redirect($this->completeURL);

                return;

            } else {

                $controller->redirect($this->confirmationURL);

                return;
            }

        }

        if (isset($result['CheckFailure']) && $result['CheckFailure']) {

            $controller->redirectBack();

        } else {

            $controller->redirect($this->failureURL);

        }

        return;
    }

}
