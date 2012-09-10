<?php

namespace Heystack\Subsystem\Payment\Test;

use Heystack\Subsystem\Payment\DPS\PXPost\Service;

use Heystack\Subsystem\Payment\DPS\PXPost\InputProcessor;

use Symfony\Component\EventDispatcher\EventDispatcher;

class PXPostTest extends \PHPUnit_Framework_TestCase
{

    protected $paymentService;

    protected function setUp()
    {
        $this->paymentService = new Service(new EventDispatcher(), new TestTransaction());
        $this->paymentService->setTestingMode(true);
    }

    protected function tearDown()
    {
        $this->paymentService = null;
    }

    public function testConfig()
    {

    }

    public function testPurchase()
    {

        $this->paymentService->setConfig(array(
            Service::CONFIG_USERNAME => 'HeydayDev',
            Service::CONFIG_PASSWORD => 'post1234'
        ));

        $this->paymentService->setUserConfig(array(
            Service::CONFIG_USER_DATA_CARD_NUMBER => '4111111111111111',
            Service::CONFIG_USER_DATA_CARD_HOLDER_NAME => 'Test Holder',
            Service::CONFIG_USER_DATA_CARD_DATE_EXPIRY => date('my'),
            Service::CONFIG_USER_DATA_CARD_CVC2 => '1234',
        ));

        $this->paymentService->processPurchase();

    }


}
